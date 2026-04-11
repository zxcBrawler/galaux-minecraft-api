<?php

namespace App\Services;

use App\Enums\UserAction;
use App\Models\User;
use App\Models\Server;
use App\Models\UserLog;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class UserActionService
{
    public function updateStatus(User $user): void
    {
        $user->update([
            'is_online' => true,
            'last_seen' => now()
        ]);
    }

    public function updateSettings(User $user, array $settings): void
    {
        $user->update($settings);
    }

    /**
     * @throws Exception
     */
    public function joinServer(User $user, Server $server): array
    {
        if ($user->is_banned) {
            throw new Exception("Вы заблокированы и не можете войти на сервер.");
        }

        UserLog::create([
            'user_id'   => $user->id_user,
            'action'    => UserAction::JOIN_SERVER,
            'server_id' => $server->id_server,
            'details'   => "Запуск клиента для сервера $server->name (IP: $server->ip)"
        ]);

        return [
            'ip' => $server->ip,
            'version' => $server->mc_version,
            'auth_token' => $user->uuid,
            'status' => 'ready'
        ];
    }

    public function getUserLogs(User $user, ?int $targetUserId = null): LengthAwarePaginator
    {
        $idToFetch = $targetUserId ?? $user->id_user;

        return UserLog::query()
            ->where('user_id', $idToFetch)
            ->with([
                'server:id_server,name',
                'user:id_user,login,name'
            ])
            ->latest('created_at')
            ->paginate(10);
    }
}
