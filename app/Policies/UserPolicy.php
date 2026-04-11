<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $currentUser): bool
    {
        return in_array($currentUser->role, [UserRole::ADMIN, UserRole::MODERATOR]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $currentUser, User $targetUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $currentUser, User $targetUser): bool
    {

        return $currentUser->id_user === $targetUser->id_user
            || $currentUser->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $targetUser): bool
    {
        return $currentUser->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $currentUser, User $targetUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $currentUser, User $targetUser): bool
    {
        return false;
    }

    public function viewLogs(User $currentUser, User $targetUser): bool
    {
        return $currentUser->id_user === $targetUser->id_user
            || $currentUser->role === UserRole::ADMIN
            || $targetUser->parent_id === $currentUser->id_user;
    }

    public function emergencyBan(User $user, User $child): bool
    {


        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        return $child->parent_id !== null && (int)$child->parent_id === (int)$user->id_user;
    }
}
