<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @property int $id_post
 * @property string $content
 * @property int $user_id
 * @property-read User $user
 * @property-read Collection|Comment[] $comments
 * @method static Post create(array $attributes)
 * @method static Builder|Post query()
 */
class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id_post';

    protected $fillable = ['content', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id', 'id_post');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'post_id', 'id_post');
    }
}
