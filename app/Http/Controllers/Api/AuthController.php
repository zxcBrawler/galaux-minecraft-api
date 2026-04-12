<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'login'     => 'required|string|max:255|unique:users,login',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'required|string|min:6',
            'is_child'  => 'required|boolean',
            'parent_id' => 'nullable|integer|exists:users,id_user',
        ]);

        $result = $this->authService->register($validated);

        return response()->json($result, 201);
    }
    /**
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $result = $this->authService->attemptLogin(
                $credentials['login'],
                $credentials['password']
            );

            return response()->json($result);

        } catch (AuthenticationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Ошибка валидации',
                'errors'  => $e->errors()
            ], 403);
        }
    }
    /**
     *
     * @unauthenticated
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);
        $result = $this->authService->refreshToken($request->refresh_token);
        return response()->json($result);
    }
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
