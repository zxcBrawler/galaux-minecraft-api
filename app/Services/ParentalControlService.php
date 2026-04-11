<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLog;
use App\Models\ServerMember;
use App\Enums\UserAction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ParentalControlService
{
    public function linkChild(User $parent, int $childId): void
    {
        $child = User::findOrFail($childId);

        if ($parent->id_user == $childId) {
            throw ValidationException::withMessages(['child_id' => 'Нельзя стать родителем самому себе']);
        }

        if ($child->parent_id !== null) {
            throw ValidationException::withMessages(['child_id' => 'Этот ребенок уже привязан к другому родителю']);
        }

        $child->update([
            'parent_id' => $parent->id_user,
            'is_child' => true
        ]);
    }

    public function updateSettings(User $child, array $data): User
    {
        $child->update($data);
        return $child;
    }

    public function getChildrenList(User $parent): Collection
    {
        return $parent->children()
            ->select('id_user', 'name', 'login', 'last_seen', 'is_online')
            ->get();
    }

    public function emergencyBan(User $child, User $parent): void
    {
        $child->update([
            'is_banned' => true,
            'banned_date_start' => now(),
            'banned_date_end' => now()->addYears(100),
        ]);
        $child->tokens()->delete();

        UserLog::create([
            'user_id'   => $child->id_user,
            'action'    => UserAction::EMERGENCY_BAN,
            'details'   => "Экстренная блокировка родителем ($parent->name)"
        ]);
    }

    public function getChildSummary(User $child): array
    {
        return [
            'time_today' => $this->calculateTimeToday($child),
            'friends_count' => $child->friends()->count(),
            'servers_visited' => ServerMember::where('user_id', $child->id_user)->count(),
            'notifications_count' => UserLog::where('user_id', $child->id_user)
                ->where('created_at', '>=', now()->subDay())
                ->whereIn('action', [UserAction::SOS_CLICK, UserAction::EMERGENCY_BAN])
                ->count(),
            'status' => [
                'is_online' => (bool)$child->is_online,
                'last_seen_text' => $child->last_seen ? $child->last_seen->diffForHumans() : 'недавно'
            ],
            'settings' => $child->parent_settings ?? []
        ];
    }

    private function calculateTimeToday(User $user): string
    {
        $logs = UserLog::where('user_id', $user->id_user)
            ->whereIn('action', [
                UserAction::LOGIN,
                UserAction::LOGOUT,
                UserAction::JOIN_SERVER,
                UserAction::LEAVE_SERVER
            ])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        $totalSeconds = 0;
        $lastLoginTime = null;

        foreach ($logs as $log) {
            if (in_array($log->action, [UserAction::LOGIN, UserAction::JOIN_SERVER])) {
                $lastLoginTime = $log->created_at;
            }
            elseif (in_array($log->action, [UserAction::LOGOUT, UserAction::LEAVE_SERVER]) && $lastLoginTime) {
                $totalSeconds += $log->created_at->diffInSeconds($lastLoginTime);
                $lastLoginTime = null;
            }
        }

        if ($lastLoginTime && $user->is_online) {
            $totalSeconds += now()->diffInSeconds($lastLoginTime);
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds / 60) % 60);

        $hourText = $this->getHourWord((int)$hours);

        return $hours > 0
            ? "$hours $hourText $minutes мин."
            : "$minutes мин.";
    }

    private function getHourWord(int $hours): string
    {
        $mod = $hours % 10;
        $mod100 = $hours % 100;

        if ($mod == 1 && $mod100 != 11) return "час";
        if (($mod >= 2 && $mod <= 4) && ($mod100 < 10 || $mod100 >= 20)) return "часа";
        return "часов";
    }
}
