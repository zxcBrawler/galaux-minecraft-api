<?php

namespace App\Enums;

enum IncidentType: string
{
    case FEAR = 'fear';         // "Мне страшно" (красная кнопка)
    case CONFUSED = 'confused'; // "Не понимаю" (желтая кнопка)
    case OTHER = 'other';       // "Другое" (синяя кнопка)
}
