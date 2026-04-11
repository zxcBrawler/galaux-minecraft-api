<?php

namespace App\Enums;

enum UserAction: string
{
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case JOIN_SERVER = 'join_server';
    case LEAVE_SERVER = 'leave_server';
    case SERVER_KICK = 'server_kick';
    case SOS_CLICK = 'sos_click';
    case EMERGENCY_BAN = 'emergency_ban';
    case ROLE_CHANGED = 'role_changed';
    case LINK_CHILD = 'link_child';
    case ITEM_BOUGHT = 'item_bought';
}
