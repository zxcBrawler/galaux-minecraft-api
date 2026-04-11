<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentChild extends Model
{
    protected $table = 'parent_child';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['parent_id', 'child_id', 'created_at'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id', 'id_user');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(User::class, 'child_id', 'id_user');
    }
}
