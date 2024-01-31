<?php

namespace App\Enums;

enum RuleV1Enum: string
{
    case VISIBLE = 'visible';
    case BECH32 = 'bech32';
    case POLICY = 'policy';
    case BALLOT_ELIGIBLE = 'ballot-eligible';
    case FEATURE_PETITION = 'feature-petition';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
