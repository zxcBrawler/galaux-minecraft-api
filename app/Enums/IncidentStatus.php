<?php

namespace App\Enums;

enum IncidentStatus: string
{
    case OPEN = 'open';               // Инцидент создан, ждет модератора
    case IN_PROGRESS = 'in_progress'; // Модератор подключился
    case RESOLVED = 'resolved';       // Проблема решена (ребенок нажал "Да")
    case CLOSED = 'closed';           // Закрыто модератором/системой
}
