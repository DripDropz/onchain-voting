<?php

namespace App\Enums;

enum BallotTypeEnum: string
{
    case SNAPSHOT = 'snapshot';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
