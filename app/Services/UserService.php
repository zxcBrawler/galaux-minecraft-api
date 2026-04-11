<?php

namespace App\Services;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function getAllUsers(int $perPage = 10): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name'          => $data['name'],
            'login'         => $data['login'],
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'uuid'          => Str::uuid()->toString(),
            'role'          => $data['role'] ?? UserRole::USER,
        ]);
    }

    public function updateUser(User $user, array $data, User $actor): User
    {
        if (isset($data['role']) && $actor->role !== UserRole::ADMIN) {
            unset($data['role']);
        }

        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
}
