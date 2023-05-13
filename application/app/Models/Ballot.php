<?php

namespace App\Models;

use App\Enums\BallotTypeEnum;
use App\Enums\ModelStatusEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use OwenIt\Auditing\Contracts\Auditable;

class Ballot extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasHashIds, HashIdModel;

    protected $fillable = [
        'title',
        'description',
        'version',
        'status',
        'type',
        'started_at'
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
    ];

    protected $casts = [
        'type' => BallotTypeEnum::class,
        'status' => ModelStatusEnum::class,
        'started_at' => 'datetime:Y-m-d H:i:s',
    ];
}
