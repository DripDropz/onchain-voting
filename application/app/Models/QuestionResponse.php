<?php

namespace App\Models;

use App\Models\QuestionChoice;
use OwenIt\Auditing\Auditable;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuestionResponse extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser,
        SoftDeletes;

    protected $hidden = [
        'id',
        'model_id',
        'question_id',
    ];

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
//        'choices' => 'array',
    ];

    protected $appends = [
        'hash',
        'question_hash',
    ];

    protected $with = [
        'choices'
    ];

    public function questionHash(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->question->hash
        );
    }

    public function voting_power(): BelongsTo
    {
        return $this->belongsTo(VotingPower::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The separate table facilitates rank choice question where a
     * response can have more than choices
     * @return BelongsToMany
     */
    public function choices(): BelongsToMany
    {
        return $this->belongsToMany(QuestionChoice::class, 'question_responses_question_choices');
    }

    public function choice(): HasOne
    {
        return $this->choices()->one()->ofMany();
    }
}
