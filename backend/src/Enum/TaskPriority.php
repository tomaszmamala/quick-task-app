<?php

namespace App\Enum;

enum TaskPriority: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
