<?php

namespace App\Services;

use App\Models\Mod;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class ModService
{
    public function getAllMods(): Collection
    {
        return Mod::all();
    }

    public function createMod(array $data, User $actor): Mod
    {
        $this->ensureIsAdmin($actor);
        return Mod::create($data);
    }

    public function updateMod(Mod $mod, array $data, User $actor): Mod
    {
        $this->ensureIsAdmin($actor);
        $mod->update($data);
        return $mod;
    }

    public function deleteMod(Mod $mod, User $actor): void
    {
        $this->ensureIsAdmin($actor);
        $mod->delete();
    }

    private function ensureIsAdmin(User $user): void
    {
        if ($user->role !== UserRole::ADMIN) {
            throw new AuthorizationException('Только администратор может управлять списком модов');
        }
    }
}
