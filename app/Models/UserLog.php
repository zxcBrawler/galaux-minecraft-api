<?php

namespace App\Models;

use App\Enums\UserAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
/**
 * @property int $id_log
 * @property int $user_id
 * @property UserAction $action
 * @property int|null $server_id
 * @property string|null $details
 * @property Carbon $created_at
 */
class UserLog extends Model
{
    protected $table = 'user_logs';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'server_id', 'details', 'created_at'];

    protected $casts = [
        'action' => UserAction::class,
        'created_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->created_at = now());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'id_server');
    }
}
