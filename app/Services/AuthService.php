<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\RefreshToken;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'uuid'          => (string) Str::uuid(),
            'name'          => $data['name'],
            'login'         => $data['login'],
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'is_child'      => (bool) ($data['is_child'] ?? false),
            'parent_id'     => ($data['is_child'] ?? false) ? ($data['parent_id'] ?? null) : null,
            'role'          => UserRole::USER,
        ]);

        return array_merge(
            $this->issueTokens($user->email, $data['password']),
            ['user' => $user]
        );
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

        return $this->issueTokens($user->email, $password);
    }

    public function refreshToken(string $refreshToken): array
    {
        return $this->proxyRequest([
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);
    }

    public function logout(User $user): void
    {
        $token = $user->token();
        if ($token) {
            $token->revoke();
            RefreshToken::where('access_token_id', $token->id)->update(['revoked' => true]);
        }
    }

    public function logoutEverywhere(User $user): void
    {
        $user->tokens->each(function ($token) {
            $token->revoke();
        });
    }

    private function issueTokens(string $email, string $password): array
    {
        return $this->proxyRequest([
            'grant_type' => 'password',
            'username'   => $email,
            'password'   => $password,
        ]);
    }

    private function proxyRequest(array $params): array
    {
        $request = Request::create('/oauth/token', 'POST', array_merge([
            'client_id'     => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'scope'         => '',
        ], $params));

        $response = app()->handle($request);

        if (!$response->isSuccessful()) {
            $data = json_decode($response->getContent(), true);
            throw new AuthenticationException(
                'Ошибка авторизации: ' . ($data['message'] ?? 'Invalid credentials')
            );
        }
        return json_decode($response->getContent(), true);
    }
}
