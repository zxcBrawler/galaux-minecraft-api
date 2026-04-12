<?php

namespace App\Interfaces;

use App\Models\Server;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServerInterface
{
    public function getServerList(?string $searchTerm = null, int $perPage = 10): LengthAwarePaginator;
    public function getServerById(Server $server): Server;
    public function createServer(array $data, User $owner): Server;
    public function updateServer(Server $server, array $data, User $actor): Server;
    public function deleteServer(Server $server, User $actor): bool;
}
