<?php

namespace App\Models;

use App\Enums\MessagePrivacy;
use App\Enums\ServerRole;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id_user
 * @property string $name
 * @property string $login
 * @property string $password_hash
 * @property string uuid
 * @property float $money
 * @property UserRole $role
 * @method static User create(array $attributes)
 * @property bool $is_banned
 * @property bool $is_online
 * @property bool $is_quiet_mode
 * @property Carbon|null $banned_date_end
 * @property Carbon|null $banned_date_start
 * @property MessagePrivacy $who_can_message
 * @property array|null $parent_settings json
 * @property bool $is_private
 * @property int $parent_id
 * @property string $yandex_id
 * @property string $vkontakte_id
 * @property Carbon|null $last_seen
 * @method static Builder|User query()
 */
class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'login',
        'password_hash',
        'email',
        'uuid',
        'is_online',
        'cosmetics',
        'role',
        'is_banned',
        'banned_date_start',
        'banned_date_end',
        'money',
        'telegram_link',
        'discord_link',
        'profile_info',
        'is_private',
        'is_child',
        'parent_id',
        'parent_settings',
        'who_can_message',
        'last_seen',
        'is_quiet_mode',
        'yandex_id',
        'vkontakte_id',
    ];

    protected $hidden = [
        'password_hash'
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_banned' => 'boolean',
        'who_can_message' => MessagePrivacy::class,
        'role' => UserRole::class,
        'is_private' => 'boolean',
        'is_child' => 'boolean',
        'money' => 'decimal:2',
        'cosmetics' => 'array',
        'parent_settings' => 'array',
        'banned_date_start' => 'datetime',
        'banned_date_end' => 'datetime',
        'last_seen' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_quiet_mode' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function ownedServers(): HasMany
    {
        return $this->hasMany(Server::class, 'owner_id', 'id_user');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'id_user');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id', 'id_user');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'user_id', 'id_user');
    }

    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friends', 'user_subscriber_id', 'user_target_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id', 'id_user');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id', 'id_user');
    }
    public function isServerOwner(int $serverId): bool
    {
        return ServerMember::where('server_id', $serverId)
            ->where('user_id', $this->id_user)
            ->where('role', ServerRole::OWNER)
            ->exists();
    }

    public function findForPassport($username)
    {
        return $this->where('login', $username)->orWhere('email', $username)->first();
    }

    public function validateForPassportPasswordGrant($password): bool
    {
        return Hash::check($password, $this->password_hash);
    }
}
