<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_comment
 * @property string $content
 * @property int $user_id
 * @property int $post_id
 * @property-read User $user
 * @property-read Post $post
 * @method static Builder|Comment query()
 * @method static Comment create(array $attributes)
 * @method static Comment findOrFail($id)
 */
class Comment extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id_comment';

    protected $fillable = ['content', 'user_id', 'post_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id_post');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'comment_id', 'id_comment');
    }
}
