<?php

namespace App\Models;


use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use OwenIt\Auditing\Contracts\Auditable;

class Signature extends Model implements Auditable, HasUser
{
    use \OwenIt\Auditing\Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser;

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash'
    ];

}
