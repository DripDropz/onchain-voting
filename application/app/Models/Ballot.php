<?php

namespace App\Models;

use App\Enums\BallotTypeEnum;
use App\Enums\ModelStatusEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;
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
        'started_at',
        'ended_at'
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
        'live'
    ];

    protected $casts = [
        'type' => BallotTypeEnum::class,
        'status' => ModelStatusEnum::class,
        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function live(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->started_at?->lte(Carbon::now()))
        );
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function choices(): HasManyThrough
    {
        return $this->hasManyThrough(BallotQuestionChoice::class, Question::class, 'ballot_id', 'question_id');
    }
}
