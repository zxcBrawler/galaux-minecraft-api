<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    public function register(array $data): array
    {
        $userData = [
            'uuid'          => (string) Str::uuid(),
            'name'          => $data['name'],
            'login'         => $data['login'],
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'is_child'      => (bool) $data['is_child'],
            'role'          => UserRole::USER,
        ];

        if ($userData['is_child'] && isset($data['parent_id'])) {
            $userData['parent_id'] = $data['parent_id'];
        }

        $user = User::create($userData);

        $token = $user->createToken('minecraft_access_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ];
    }

    /**
     * @throws AuthenticationException
     */
    public function attemptLogin(string $login, string $password): array
    {
        $user = User::where('login', $login)->first();

        if (!$user || !Hash::check($password, $user->password_hash)) {
            throw new AuthenticationException('Неверный логин или пароль');
        }

        if ($user->is_banned) {
            $date = $user->banned_date_end ? $user->banned_date_end->format('d.m.Y H:i') : 'навсегда';
            throw ValidationException::withMessages([
                'login' => ["Ваш аккаунт заблокирован до: $date"]
            ]);
        }

        $token = $user->createToken('minecraft_access_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];
    }
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
