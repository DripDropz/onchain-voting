<?php

namespace App\Enums;

enum RuleTypeEnum: string
{
    case POLICY = 'policy';
    case SUM = 'sum';
    case COUNT = 'count';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
