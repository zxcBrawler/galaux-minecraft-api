<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Auth\Access\AuthorizationException;

class CommentService
{
    public function getCommentsByPost(Post $post, int $perPage = 15): LengthAwarePaginator
    {
        return $post->comments()
            ->with('user:id_user,name,login')
            ->latest()
            ->paginate($perPage);
    }

    public function createComment(User $user, array $data): Comment
    {
        return $user->comments()->create($data);
    }

    public function deleteComment(Comment $comment, User $user): void
    {
        if ($user->cannot('delete', $comment)) {
            throw new AuthorizationException('Вы не можете удалить этот комментарий');
        }

        $comment->delete();
    }
}
