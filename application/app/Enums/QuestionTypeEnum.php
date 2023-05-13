<?php

namespace App\Enums;

enum QuestionTypeEnum: string
{
    case SINGLE = 'single';
    case MULTIPLE = 'multiple';
    case RANKED = 'ranked';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
