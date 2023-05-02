<?php

namespace App\Enums;

enum ModelStatusEnum: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PUBLISHED = 'published';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
