<?php

namespace App\Http\Controllers\Api;

use App\Enums\IncidentType;
use App\Enums\MessagePrivacy;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\User;
use App\Services\UserActionService;
use App\Services\ParentalControlService;
use App\Services\IncidentService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserActionController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected UserActionService $actionService,
        protected ParentalControlService $parentalService,
        protected IncidentService $incidentService
    ) {}

    public function updateStatus(Request $request)
    {
        $this->actionService->updateStatus($request->user());
        return response()->json(['message' => 'Status updated']);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'is_private' => 'sometimes|boolean',
            'who_can_message' => ['sometimes', new Enum(MessagePrivacy::class)],
        ]);

        $this->actionService->updateSettings($request->user(), $validated);
        return response()->json(['message' => 'Settings updated']);
    }

    public function updateParentalSettings(Request $request, User $id_user)
    {
        if ($request->user()->role !== UserRole::ADMIN && $id_user->parent_id !== $request->user()->id_user) {
            return response()->json(['message' => 'Доступ запрещен.'], 403);
        }

        $validated = $request->validate([
            'is_child' => 'sometimes|boolean',
            'parent_settings' => 'sometimes|array'
        ]);

        $user = $this->parentalService->updateSettings($id_user, $validated);

        return response()->json(['message' => 'Parental settings updated', 'user' => $user]);
    }

    public function joinServer(Request $request, Server $id_server)
    {
        try {
            $sessionData = $this->actionService->joinServer(
                $request->user(),
                $id_server
            );

            return response()->json([
                'message' => 'Клиент готов к запуску',
                'data'    => $sessionData
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function linkChild(Request $request, $child_id)
    {
        $this->parentalService->linkChild($request->user(), (int)$child_id);
        return response()->json(['message' => 'Ребенок успешно привязан']);
    }

    public function getLogs(Request $request)
    {
        $logs = $this->actionService->getUserLogs($request->user());
        return response()->json($logs);
    }

    public function getChildLogs(Request $request, int $id_user)
    {
        $targetUser = User::findOrFail($id_user);
        $this->authorize('viewLogs', $targetUser);

        $logs = $this->actionService->getUserLogs($request->user(), $id_user);

        return response()->json($logs);
    }

    public function getChildren(Request $request)
    {
        return response()->json($this->parentalService->getChildrenList($request->user()));
    }

    public function getChildSummary(User $id_user)
    {
        $this->authorize('viewLogs', $id_user);
        return response()->json($this->parentalService->getChildSummary($id_user));
    }

    public function childEmergencyBan(Request $request, User $id_user)
    {
        Log::info('Debug Emergency Ban:', [
            'parent_id_from_child' => $id_user->parent_id,
            'current_user_id'      => auth()->id(),
            'are_they_equal'       => (int)$id_user->parent_id === (int)auth()->id(),
            'child_model_exists'   => $id_user->exists,
        ]);
        $this->authorize('emergencyBan', $id_user);

        $this->parentalService->emergencyBan($id_user, $request->user());
        return response()->json(['message' => "Доступ для {$id_user->name} ограничен"]);
    }

    public function childSendSos(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(IncidentType::class)],
            'id_server' => 'nullable|exists:servers,id_server',
        ]);

        $incident = $this->incidentService->createSos($request->user(), $validated);

        return response()->json([
            'message' => 'Ты в безопасности. Мы уже помогаем!',
            'incident_id' => $incident->id_incident
        ], 201);
    }
}
