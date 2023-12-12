<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;

class BallotResponse extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser,
        SoftDeletes;

    protected $hidden = [
        'id',
        'ballot_id',
        'question_id',
    ];

    protected $fillable = [
        'ballot_id',
        'question_id',
        'user_id',
        'voting_power_id',
        'submit_tx',
        'rank'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
//        'choices' => 'array',
    ];

    protected $appends = [
        'ballot_hash',
        'question_hash',
    ];

    public function ballotHash(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->ballot->hash
        );
    }

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

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function choices(): BelongsToMany
    {
        return $this->belongsToMany(BallotQuestionChoice::class, 'ballot_responses_ballot_question_choices');
    }

    public function choice(): HasOne
    {
        return $this->choices()->one()->ofMany();
    }
}
