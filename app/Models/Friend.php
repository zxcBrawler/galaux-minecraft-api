<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friend extends Model
{
    protected $table = 'friends';
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = ['user_subscriber_id', 'user_target_id', 'status'];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_subscriber_id', 'id_user');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_target_id', 'id_user');
    }
}
