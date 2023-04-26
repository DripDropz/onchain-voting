<?php

namespace App\Models;

use App\Enums\BallotTypeEnum;
use App\Enums\ModelStatusEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Ballot extends Model
{
    use HasHashIds, HashIdModel;

    protected $fillable = [
        'title',
        'description',
        'version',
        'status',
        'type',
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
    ];
}
