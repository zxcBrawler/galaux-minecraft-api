<?php

namespace App\Services\Cache;

use App\Interfaces\ServerInterface;
use App\Models\Server;
use App\Models\User;
use App\Services\ServerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class CachingServerService implements ServerInterface
{
    public function __construct(protected ServerService $baseService) {}

    public function getServerList(?string $searchTerm = null, int $perPage = 10): LengthAwarePaginator
    {
        $page = request('page', 1);
        $cacheKey = "servers_list_" . md5("s_{$searchTerm}_p_{$page}_pp_{$perPage}");

        $data = Cache::remember($cacheKey, 3600, function () use ($searchTerm, $perPage) {
            $paginator = $this->baseService->getServerList($searchTerm, $perPage);
            return [
                'items' => $paginator->getCollection()->toArray(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
            ];
        });

        return new LengthAwarePaginator(
            $data['items'], $data['total'], $data['per_page'], $data['current_page'],
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function getServerById(Server $server): Server
    {
        $cacheKey = "server_show_{$server->id_server}";

        $attributes = Cache::remember($cacheKey, 3600, function () use ($server) {
            return $this->baseService->getServerById($server)->toArray();
        });

        return new Server()->forceFill($attributes);
    }

    public function createServer(array $data, User $owner): Server
    {
        return $this->baseService->createServer($data, $owner);
    }

    public function updateServer(Server $server, array $data, User $actor): Server
    {
        return $this->baseService->updateServer($server, $data, $actor);
    }

    public function deleteServer(Server $server, User $actor): bool
    {
        return $this->baseService->deleteServer($server, $actor);
    }
}
