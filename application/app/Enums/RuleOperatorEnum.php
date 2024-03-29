<?php

namespace App\Enums;

enum RuleOperatorEnum: string
{
    case EQUALS = '=';
    case LESS_THAN = '<';
    case GREATER_THAN = '>';
    case EQUALS_OR_GREATER_THAN = '>=';
    case EQUALS_OR_LESS_THAN = '<=';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
