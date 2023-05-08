<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self update_ballot()
 * @method static self read_ballot()
 * @method static self create_ballot()
 * @method static self delete_ballot()
 */
final class PermissionEnum extends Enum
{
    protected static function values(): \Closure
    {
        return fn (string $name): string|int => str_replace('_', ' ', mb_strtolower($name));
    }
}
