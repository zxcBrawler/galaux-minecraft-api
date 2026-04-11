<?php
namespace App\Enums;

enum MessagePrivacy: string
{
    case ALL = 'all';
    case FRIENDS = 'friends';
    case NOBODY = 'nobody';

    public function label(): string
    {
        return match($this) {
            self::ALL => 'Все пользователи',
            self::FRIENDS => 'Только друзья',
            self::NOBODY => 'Никто',
        };
    }
}
