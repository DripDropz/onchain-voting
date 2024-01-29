<?php

namespace App\Enums;

enum RuleTypeEnum: string
{
    case FT = 'ft';
    case NFT = 'nft';
    case TALLY = 'tally';
    case POOL = 'pool';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
