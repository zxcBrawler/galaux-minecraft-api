<?php

use App\Http\Controllers\Api\{AuthController,
    ServerMemberController,
    UserController,
    ServerController,
    ModController,
    ShopController,
    ProductController,
    PurchaseController,
    PostController,
    CommentController,
    LikeController,
    UserActionController,
    SocialAuthController};
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('user')->group(function () {
        Route::patch('/status', [UserActionController::class, 'updateStatus']);
        Route::put('/settings', [UserActionController::class, 'updateSettings']);
        Route::post('/sos', [UserActionController::class, 'childSendSos']);
        Route::get('/logs', [UserActionController::class, 'getLogs']);
        Route::get('/children', [UserActionController::class, 'getChildren']);
        Route::post('/link-child/{child_id}', [UserActionController::class, 'linkChild']);
    });

    Route::prefix('users/{id_user}')->group(function () {
        Route::get('/child-summary', [UserActionController::class, 'getChildSummary']);
        Route::put('/parental-control', [UserActionController::class, 'updateParentalSettings']);
        Route::post('/ban', [UserActionController::class, 'childEmergencyBan']);
        Route::get('/child-logs', [UserActionController::class, 'getChildLogs']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'getUsers']);
        Route::get('/{id_user}', [UserController::class, 'getUser']);
        Route::post('/', [UserController::class, 'createUser']);
        Route::put('/{id_user}', [UserController::class, 'updateUser']);
        Route::delete('/{id_user}', [UserController::class, 'deleteUser']);
    });

    Route::prefix('servers')->group(function () {
        Route::get('/', [ServerController::class, 'getServers']);
        Route::get('/{id_server}', [ServerController::class, 'getServer']);
        Route::post('/', [ServerController::class, 'createServer']);
        Route::put('/{id_server}', [ServerController::class, 'updateServer']);
        Route::delete('/{id_server}', [ServerController::class, 'deleteServer']);
    });

    Route::prefix('mods')->group(function () {
        Route::get('/', [ModController::class, 'getMods']);
        Route::post('/', [ModController::class, 'createMod']);
        Route::put('/{id_mod}', [ModController::class, 'updateMod']);
        Route::delete('/{id_mod}', [ModController::class, 'deleteMod']);
    });

    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'getPosts']);
        Route::post('/', [PostController::class, 'createPost']);
        Route::get('/{id_post}', [PostController::class, 'getPost']);
        Route::put('/{id_post}', [PostController::class, 'updatePost']);
        Route::delete('/{id_post}', [PostController::class, 'deletePost']);

        Route::post('/{id_post}/like', [LikeController::class, 'likePost']);
        Route::delete('/{id_post}/like', [LikeController::class, 'unlikePost']);
        Route::get('/{id_post}/comments', [CommentController::class, 'getCommentsByPostId']);
    });

    Route::get('/servers/{id_server}/shop', [ShopController::class, 'getShopWithProducts']);
    Route::post('/servers/{id_server}/shop', [ShopController::class, 'createServerShop']);

    Route::prefix('products')->group(function () {
        Route::get('/{id_product}', [ProductController::class, 'getProductById']);
        Route::post('/{id_product}/buy', [PurchaseController::class, 'buyProduct']);
    });

    Route::post('/comments', [CommentController::class, 'addCommentToPost']);
    Route::delete('/comments/{id_comment}', [CommentController::class, 'deleteComment']);

    Route::prefix('servers/{id_server}')->group(function () {
        Route::get('/members', [ServerMemberController::class, 'getServerMembers']);
        Route::post('/join', [UserActionController::class, 'joinServer']);
        Route::post('/addMember', [ServerMemberController::class, 'addMember']);
        Route::put('/members/{id_user}/role', [ServerMemberController::class, 'updateRole']);
        Route::delete('/members/{id_user}', [ServerMemberController::class, 'removeMemberFromServer']);
    });
});
