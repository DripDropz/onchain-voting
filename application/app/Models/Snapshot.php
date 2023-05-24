<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use OwenIt\Auditing\Contracts\Auditable;

class Snapshot extends Model implements Auditable, HasUser
{
    use \OwenIt\Auditing\Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser;

    protected $fillable = [
        'title',
        'description',
        'policy_id',
        'status',
        'type'
    ];

    protected $appends = [
        'hash',
    ];
}
