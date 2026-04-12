<?php

namespace App\Services;

use App\Interfaces\ServerInterface;
use App\Models\Server;
use App\Models\User;
use App\Enums\ServerRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;

class ServerService implements ServerInterface
{
    public function getServerList(?string $searchTerm = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Server::query()->with([
            'owner:id_user,name,login',
            'tags',
            'images' => fn($q) => $q->where('is_main', true)
        ]);

        if ($searchTerm) {
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function getServerById($id_server): Server
    {
        return Server::with([
            'owner:id_user,name,login',
            'tags',
            'images',
            'shop',
            'mods',
            'members.user:id_user,name'
        ])->findOrFail($id_server);
    }

    public function createServer(array $data, User $owner): Server
    {
        $server = Server::create($data);
        $server->members()->create([
            'user_id' => $owner->id_user,
            'role' => ServerRole::OWNER,
            'joined_at' => now()
        ]);
        return $server;
    }

    public function updateServer(Server $server, array $data, User $actor): Server
    {
        Gate::forUser($actor)->authorize('update', $server);
        $server->update($data);
        return $server;
    }

    public function deleteServer(Server $server, User $actor): bool
    {
        Gate::forUser($actor)->authorize('delete', $server);
        return $server->delete();
    }
}
