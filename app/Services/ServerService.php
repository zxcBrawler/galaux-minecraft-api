<?php

namespace App\Services;

use App\Models\Server;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\ServerRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;

class ServerService
{
    public function getServerList(?string $searchTerm = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Server::query()
            ->with([
                'owner:id_user,name,login',
                'tags',
                'images' => function ($query) {
                    $query->where('is_main', true);
                }
            ]);

        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        return $query->latest()->paginate($perPage);
    }
    public function getServerById(Server $server): Server
    {
        return $server->load([
            'owner:id_user,name,login',
            'tags',
            'images',
            'shop',
            'mods',
            'members.user:id_user,name'
        ]);
    }

    public function createServer(array $data, User $owner): Server
    {
        $server = Server::create($data);

        $server->members()->create([
            'user_id'   => $owner->id_user,
            'role'      => ServerRole::OWNER,
            'joined_at' => now()
        ]);

        return $server;
    }

    public function updateServer(Server $server, array $data, User $actor): Server
    {
        if (!$actor->isServerOwner($server->id_server) && $actor->role !== UserRole::ADMIN) {
            throw new AuthorizationException('У вас нет прав для редактирования этого сервера.');
        }

        $server->update($data);
        return $server;
    }

    public function deleteServer(Server $server, User $actor): bool
    {
        if (!$actor->isServerOwner($server->id_server) && $actor->role !== UserRole::ADMIN) {
            throw new AuthorizationException('У вас нет прав для удаления этого сервера.');
        }

        return $server->delete();
    }
}
