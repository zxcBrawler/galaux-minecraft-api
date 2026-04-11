<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class LikeService
{
    public function likePost(Post $post, User $user): Model
    {
        $like = $post->likes()->firstOrCreate([
            'user_id' => $user->id_user
        ]);

        if (!$like->wasRecentlyCreated) {
            throw ValidationException::withMessages(['post' => 'Вы уже поставили лайк этому посту']);
        }

        return $like;
    }

    public function unlikePost(Post $post, User $user): bool
    {
        return $post->likes()
                ->where('user_id', $user->id_user)
                ->delete() > 0;
    }
}
