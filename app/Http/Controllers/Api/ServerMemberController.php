<?php

namespace App\Http\Controllers\Api;

use App\Enums\ServerRole;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Services\ServerMemberService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ServerMemberController extends Controller
{
    public function __construct(
        protected ServerMemberService $memberService
    ) {}

    public function getServerMembers(Server $id_server)
    {
        return response()->json($this->memberService->getTeamList($id_server));
    }
    public function addMember(Request $request, Server $id_server)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id_user',
            'role'    => ['required', new Enum(ServerRole::class)],
        ]);

        $member = $this->memberService->addTeamMember(
            $id_server,
            $data['user_id'],
            ServerRole::from($data['role']),
            auth()->user()
        );

        return response()->json(['message' => 'Участник добавлен!', 'data' => $member]);
    }

    public function updateRole(Request $request, Server $id_server, $id_user)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:owner,admin,moderator,builder'
        ]);

        $this->memberService->updateMemberRole($id_server, (int)$id_user, $validated['role'], $request->user());

        return response()->json(['message' => "Пользователю назначена роль: {$validated['role']}"]);
    }

    public function removeMemberFromServer(Request $request, Server $id_server, $id_user)
    {
        $this->memberService->removeMember($id_server, (int)$id_user, $request->user());

        return response()->json(['message' => 'Участник успешно исключен из команды сервера.']);
    }
}
