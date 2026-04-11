<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_like
 * @property int $user_id
 * @property int|null $post_id
 * @property int|null $comment_id
 * @method static Like create(array $attributes)
 * @method static Builder where(string $column, mixed $value)
 */
class Like extends Model
{
    protected $table = 'likes';
    protected $primaryKey = 'id_like';

    protected $fillable = ['user_id', 'post_id', 'comment_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id_post');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id_comment');
    }
}
