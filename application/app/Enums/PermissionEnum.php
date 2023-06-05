<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case UPDATE_BALLOT = 'update_ballot';
    case READ_BALLOT = 'read_ballot';
    case CREATE_BALLOT = 'create_ballot';
    case DELETE_BALLOT = 'delete_ballot';

    protected static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
