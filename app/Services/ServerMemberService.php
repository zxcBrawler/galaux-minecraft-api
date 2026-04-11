<?php

namespace App\Services;

use App\Enums\ServerRole;
use App\Models\Server;
use App\Models\ServerMember;
use App\Models\User;
use App\Models\UserLog;
use App\Enums\UserAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ServerMemberService
{
    public function getTeamList(Server $server, int $perPage = 15): LengthAwarePaginator
    {
        return ServerMember::where('server_id', $server->id_server)
            ->with('user:id_user,name,login,is_online,last_seen')
            ->orderByRaw("FIELD(role, 'owner', 'admin', 'moderator', 'builder')")
            ->paginate($perPage);
    }

    public function addTeamMember(Server $server, int $newUserId, ServerRole $role, User $actor): ServerMember
    {
        if (!$actor->isServerOwner($server->id_server)) {
            throw new AuthorizationException('У вас нет прав для добавления участников.');
        }

        $exists = ServerMember::where('server_id', $server->id_server)
            ->where('user_id', $newUserId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'user_id' => 'Этот пользователь уже в команде.'
            ]);
        }

        return ServerMember::create([
            'server_id' => $server->id_server,
            'user_id'   => $newUserId,
            'role'      => $role->value,
        ]);
    }
    public function updateMemberRole(Server $server, int $userId, string $newRole, User $actor): ServerMember
    {
        $operator = ServerMember::where('server_id', $server->id_server)
            ->where('user_id', $actor->id_user)
            ->first();

        if (!$operator || !in_array($operator->role->value ?? $operator->role, ['owner', 'admin'])) {
            throw new AuthorizationException('У вас нет прав для управления ролями');
        }

        $member = ServerMember::where('server_id', $server->id_server)
            ->where('user_id', $userId)
            ->firstOrFail();

        $oldRole = $member->role->value ?? $member->role;
        $member->update(['role' => $newRole]);

        UserLog::create([
            'user_id'   => $userId,
            'server_id' => $server->id_server,
            'action'    => UserAction::ROLE_CHANGED,
            'details'   => "Роль изменена с '$oldRole' на '$newRole'. Изменил: $actor->login"
        ]);

        return $member;
    }

    public function removeMember(Server $server, int $userId, User $actor): void
    {
        if (!$actor->isServerOwner($server->id_server)) {
            throw new AuthorizationException('Только владелец сервера может исключать участников.');
        }

        if ($userId === (int)$actor->id_user) {
            throw ValidationException::withMessages(['user_id' => 'Вы не можете исключить себя из собственной команды.']);
        }

        $member = ServerMember::where('server_id', $server->id_server)
            ->where('user_id', $userId)
            ->firstOrFail();

        $oldRole = $member->role->value ?? $member->role;
        $member->delete();

        UserLog::create([
            'user_id'   => $userId,
            'server_id' => $server->id_server,
            'action'    => UserAction::ROLE_CHANGED,
            'details'   => "Пользователь исключен из команды. Прежняя роль: $oldRole."
        ]);
    }
}
