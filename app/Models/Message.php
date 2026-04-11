<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id_message';

    protected $fillable = ['from_user_id', 'to_user_id', 'message', 'is_read', 'read_at'];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id_user');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id', 'id_user');
    }
}
