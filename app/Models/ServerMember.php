<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerMember extends Model
{
    protected $table = 'server_members';
    protected $primaryKey = 'id_member';

    protected $fillable = ['server_id', 'user_id', 'role', 'joined_at'];

    protected $casts = [
        'joined_at' => 'datetime'
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'id_server');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
