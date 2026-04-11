<?php

namespace App\Services;

use App\Models\User;
use App\Models\Incident;
use App\Models\UserLog;
use App\Enums\IncidentStatus;
use App\Enums\UserAction;
use App\Events\SosSignalSent;

class IncidentService
{
    public function createSos(User $user, array $data): Incident
    {
        $incident = Incident::create([
            'user_id'   => $user->id_user,
            'server_id' => $data['id_server'] ?? null,
            'type'      => $data['type'],
            'status'    => IncidentStatus::OPEN,
        ]);

        $user->update(['is_quiet_mode' => true]);

        broadcast(new SosSignalSent($incident))->toOthers();

        UserLog::create([
            'user_id'   => $user->id_user,
            'action'    => UserAction::SOS_CLICK,
            'details'   => "Ребенок активировал SOS. Тип: {$incident->type->value}. Режим тишины включен."
        ]);

        return $incident;
    }
}
