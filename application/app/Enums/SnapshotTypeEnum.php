<?php

namespace App\Enums;

enum SnapshotTypeEnum: string
{
    case FILE = 'file';
    case NETWORK = 'network';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
