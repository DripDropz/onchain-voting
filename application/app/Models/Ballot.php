<?php

namespace App\Models;

use App\Enums\BallotTypeEnum;
use App\Enums\ModelStatusEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ballot extends Model
{
    use HasHashIds, HashIdModel, HasFactory;

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
