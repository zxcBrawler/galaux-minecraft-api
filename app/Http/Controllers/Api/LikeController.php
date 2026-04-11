<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\LikeService;

class LikeController extends Controller
{
    public function __construct(protected LikeService $likeService) {}

    public function likePost(Post $id_post)
    {
        try {
            $like = $this->likeService->likePost($id_post, auth()->user());
            return response()->json($like, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function unlikePost(Post $id_post)
    {
        $success = $this->likeService->unlikePost($id_post, auth()->user());

        if (!$success) {
            return response()->json(['message' => 'Like not found'], 404);
        }

        return response()->json(['message' => 'Like removed']);
    }
}
