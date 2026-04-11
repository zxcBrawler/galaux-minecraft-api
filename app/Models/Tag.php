<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Tag extends Model
{
    protected $primaryKey = 'id_tag';
    protected $fillable = ['name', 'slug'];

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_tag', 'tag_id', 'server_id');
    }
}
