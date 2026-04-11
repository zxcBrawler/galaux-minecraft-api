<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(protected CommentService $commentService) {}

    public function getCommentsByPostId(Post $id_post)
    {
        return response()->json($this->commentService->getCommentsByPost($id_post));
    }
    public function addCommentToPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id_post',
            'content' => 'required|string|max:1000'
        ]);

        $comment = $this->commentService->createComment($request->user(), $validated);
        return response()->json($comment, 201);
    }
    public function deleteComment(Request $request, Comment $id_comment)
    {
        $this->commentService->deleteComment($id_comment, $request->user());
        return response()->json(['message' => 'Comment deleted']);
    }
}
