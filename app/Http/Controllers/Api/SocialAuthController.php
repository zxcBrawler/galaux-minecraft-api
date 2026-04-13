<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * @unauthenticated
     */
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['yandex', 'vkid'])) {
            return response()->json(['error' => 'Провайдер не поддерживается'], 400);
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * @unauthenticated
     */
    public function handleProviderCallback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $idField = $provider . '_id';

            $user = User::where($idField, $socialUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            if ($user) {
                $user->update([
                    $idField => $socialUser->getId(),
                ]);
            } else {
                $user = User::create([
                    'name'  => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'login' => $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
                    $idField => $socialUser->getId(),
                    'password_hash' => bcrypt(Str::random(24)),
                    'uuid' => Str::uuid()->toString(),
                ]);
            }

            $token = $user->createToken('Social Login')->accessToken;

            return response()->json([
                'status' => 'success',
                'token'  => $token,
                'user'   => $user,
            ]);

        } catch (\Exception $e) {
            \Log::error("VK Auth Error: " . $e->getMessage());
            if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                \Log::error("VK Detailed Response: " . $responseBody);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка авторизации: ' . $e->getMessage()
            ], 401);
        }
    }
}
