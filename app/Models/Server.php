<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id_server
 * @property string $name
 * @property string $ip
 * @property int $player_count
 * @property bool $is_online
 * @property bool $is_official
 * @property string mc_version
 * @property-read User $owner
 * @property-read Shop|null $shop
 * @property-read Collection|Mod[] $mods
 * @method static Server create(array $attributes)
 * @method static Builder|Server query()
 */
class Server extends Model
{
    use HasFactory;
    protected $table = 'servers';
    protected $primaryKey = 'id_server';

    protected $fillable = [
        'name', 'ip', 'player_count', 'is_online', 'is_official',
        'version', 'mc_version',
        'joined_at',
        'role',
        'user_id'
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_official' => 'boolean',
        'player_count' => 'integer'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id_user');
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'server_admins', 'server_id', 'user_id', 'id_server', 'id_user');
    }

    public function members() {
        return $this->hasMany(ServerMember::class, 'server_id', 'id_server');
    }

    public function mods(): BelongsToMany
    {
        return $this->belongsToMany(Mod::class, 'server_mods', 'server_id', 'mod_id', 'id_server', 'id_mod')
            ->withPivot('mod_version', 'is_required');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'server_id', 'id_server');
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'server_tag', 'server_id', 'tag_id');
    }
    public function products()
    {
        return $this->hasManyThrough(Product::class, Shop::class, 'server_id', 'shop_id', 'id_server', 'id_shop');
    }
    public function images()
    {
        return $this->hasMany(ServerImage::class, 'server_id', 'id_server');
    }
}
