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

    protected $fillable = [
        'model_id',
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
        'hash',
        'question_hash',
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

    public function choices(): BelongsToMany
    {
        return $this->belongsToMany(QuestionChoice::class, 'question_responses_question_choices');
    }

    public function choice(): HasOne
    {
        return $this->choices()->one()->ofMany();
    }
}
