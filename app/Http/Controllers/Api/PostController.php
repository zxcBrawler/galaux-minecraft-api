<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(protected PostService $postService) {}

    public function getPosts()
    {
        return response()->json($this->postService->getAllPosts());
    }
    public function getPost(Post $id_post)
    {
        return response()->json($this->postService->getPost($id_post));
    }

    public function createPost(Request $request)
    {
        $validated = $request->validate(['content' => 'required|string|max:5000']);
        $post = $this->postService->createPost($request->user(), $validated);
        return response()->json($post, 201);
    }

    public function updatePost(Request $request, Post $id_post)
    {
        $validated = $request->validate(['content' => 'sometimes|string|max:5000']);
        $post = $this->postService->updatePost($id_post, $request->user(), $validated);
        return response()->json($post);
    }

    public function deletePost(Request $request, Post $id_post)
    {
        $this->postService->deletePost($id_post, $request->user());
        return response()->json(['message' => 'Пост удален']);
    }
}
