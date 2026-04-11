<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.alerts', function ($user) {
    return in_array($user->role, [UserRole::MODERATOR, UserRole::ADMIN]);
});
