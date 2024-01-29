<?php

namespace App\Enums;

enum RuleV1Enum: string
{
    case VISIBLE = 'visible';
    case BECH23 = 'bech23';
    case POLICY = 'policy';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
