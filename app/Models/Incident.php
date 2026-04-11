<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use App\Enums\IncidentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id_incident
 * @property int $user_id
 * @property int|null $server_id
 * @property IncidentType $type
 * @property IncidentStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Incident extends Model
{
    protected $table = 'incidents';
    protected $primaryKey = 'id_incident';

    protected $fillable = [
        'user_id', 'server_id', 'type', 'status', 'moderator_id', 'moderator_joined_at'
    ];

    protected $casts = [
        'type' => IncidentType::class,
        'status' => IncidentStatus::class,
        'moderator_joined_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
