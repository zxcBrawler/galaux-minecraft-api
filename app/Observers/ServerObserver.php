<?php

namespace App\Observers;

use App\Models\Server;
use Illuminate\Support\Facades\Cache;

class ServerObserver
{
    public function updated(Server $server): void
    {
        $this->clearCache($server);
    }

    public function deleted(Server $server): void
    {
        $this->clearCache($server);
    }

    public function created(Server $server): void
    {
        $this->clearLists();
    }
    protected function clearCache(Server $server): void
    {
        Cache::forget("server_show_{$server->id_server}");
        $this->clearLists();
    }

    protected function clearLists(): void
    {
        Cache::flush();
    }
}
