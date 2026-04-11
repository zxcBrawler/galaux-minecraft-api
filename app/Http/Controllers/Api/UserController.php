<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected UserService $userService
    ) {}


    public function getUsers()
    {
        $this->authorize('viewAny', User::class);
        return response()->json($this->userService->getAllUsers());
    }

    public function getUser(User $id_user)
    {
        return response()->json($id_user);
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'login'    => 'required|string|unique:users,login',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => ['sometimes', new Enum(UserRole::class)],
        ]);

        $user = $this->userService->createUser($validated);
        return response()->json($user, 201);
    }

    public function updateUser(Request $request, User $id_user)
    {
        $this->authorize('update', $id_user);

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|unique:users,email,' . $id_user->getKey() . ',id_user',
            'profile_info' => 'sometimes|string|nullable',
            'role'         => ['sometimes', new Enum(UserRole::class)],
        ]);

        $user = $this->userService->updateUser($id_user, $validated, $request->user());
        return response()->json($user);
    }

    public function deleteUser(User $id_user)
    {
        $this->authorize('delete', $id_user);
        $this->userService->deleteUser($id_user);
        return response()->json(['message' => 'Пользователь удален']);
    }
}
