<?php

namespace App\Enums;

enum ServerRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case BUILDER = 'builder';
}
