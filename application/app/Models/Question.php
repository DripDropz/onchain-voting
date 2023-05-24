<?php

namespace App\Models;

use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Question extends Model implements Auditable, HasUser
{
    use \OwenIt\Auditing\Auditable, HasHashIds, HashIdModel, HasFactory, Traits\HasUser;

    protected $fillable = [
        'title',
        'description',
        'supplemental',
        'max_choices',
        'status',
        'type',
        'started_at'
    ];

    protected $hidden = [
        'id',
        'ballot_id',
    ];

    protected $appends = [
        'hash',
    ];

    protected $casts = [
        'type' => QuestionTypeEnum::class,
        'status' => ModelStatusEnum::class,
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(BallotQuestionChoice::class, 'question_id');
    }
}
