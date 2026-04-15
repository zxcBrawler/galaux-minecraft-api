<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\ServerInterface;
use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function __construct(
        protected ServerInterface $serverService
    ) {}

    /**
     *
     * @unauthenticated
     */
    public function getServers(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'page'   => 'nullable|integer|min:1',
        ]);
        $servers = $this->serverService->getServerList(
            $request->query('search'),
            10
        );

        return response()->json($servers);
    }

    /**
     *
     * @unauthenticated
     */
    public function getServer($id_server)
    {
        $fullServer = $this->serverService->getServerById($id_server);
        return response()->json($fullServer);
    }

    public function createServer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|string|max:45',
            'mc_version' => 'required|string|max:20',
            'version' => 'nullable|string|max:50'
        ]);

        $server = $this->serverService->createServer($validated, $request->user());

        return response()->json($server, 201);
    }

    public function updateServer(Request $request, Server $id_server)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'ip' => 'sometimes|string|max:45',
            'is_online' => 'sometimes|boolean',
            'player_count' => 'sometimes|integer',
            'is_official' => 'sometimes|boolean'
        ]);

        $server = $this->serverService->updateServer($id_server, $validated, $request->user());

        return response()->json($server);
    }

    public function deleteServer(Request $request, Server $id_server)
    {
        $this->serverService->deleteServer($id_server, $request->user());

        return response()->json(['message' => 'Сервер удален']);
    }
}
