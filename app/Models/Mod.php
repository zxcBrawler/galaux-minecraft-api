<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
/**
 * @property int $id_mod
 * @property string $name
 * @property string $mod_id
 * @property string|null $description
 * @property string|null $icon_url
 * @property string|null $modrinth_id
 * @property int|null $curseforge_id
 * @method static Mod create(array $attributes)
 * @method static Builder|Mod query()
 */
class Mod extends Model
{
    use HasFactory;
    protected $table = 'mods';
    protected $primaryKey = 'id_mod';

    protected $fillable = [
        'name', 'mod_id', 'description', 'icon_url', 'modrinth_id', 'curseforge_id'
    ];

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_mods', 'mod_id', 'server_id', 'id_mod', 'id_server')
            ->withPivot('mod_version', 'is_required');
    }
}
