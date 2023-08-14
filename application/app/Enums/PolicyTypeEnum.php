<?php

namespace App\Enums;

enum PolicyTypeEnum: string
{
    case Registration = 'registration';
    case Vote = 'voting';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid($value): bool
    {
        return in_array($value, self::values(), true);
    }
}
