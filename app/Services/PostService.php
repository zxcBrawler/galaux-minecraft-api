<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class PostService
{
    public function getAllPosts(): Collection
    {
        return Post::latest()->get();
    }
    public function getPost(Post $post): Post
    {
        return $post->load('user:id_user,login,name');
    }

    public function createPost(User $user, array $data): Post
    {
        return $user->posts()->create($data);
    }

    public function updatePost(Post $post, User $user, array $data): Post
    {
        if ($user->cannot('update', $post)) {
            throw new AuthorizationException('У вас нет прав на редактирование этого поста');
        }

        $post->update($data);
        return $post;
    }

    public function deletePost(Post $post, User $user): void
    {
        if ($user->cannot('delete', $post)) {
            throw new AuthorizationException('У вас нет прав на удаление этого поста');
        }

        $post->delete();
    }
}
