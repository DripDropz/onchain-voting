<?php

namespace App\Models;

use App\DataTransferObjects\QuestionChoiceData;
use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
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
        'started_at',
    ];

    protected $hidden = [
        'id',
        'ballot_id',
    ];

    protected $appends = [
        'hash',
        'choices_tally',
    ];

    protected $casts = [
        'type' => QuestionTypeEnum::class,
        'status' => ModelStatusEnum::class,
        'created_at' => 'datetime:Y-m-d H:i:s',
        'choices' => QuestionChoiceData::class,
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(BallotQuestionChoice::class, 'question_id');
    }

    public function ranked_user_responses():HasMany
    {
       return $this->hasMany(BallotResponse::class)->where(
            [
                'user_id' => auth()?->user()?->getAuthIdentifier(),
            ]
        )->whereNotNull('rank')->orderBy('rank', 'asc');
    }

    public function choicesTally(): Attribute
    {
        return Attribute::make(
            get: function () {
                $allChoices = BallotQuestionChoice::where('question_id', $this->id)
                    ->pluck('title')
                    ->toArray();

                return [];

//                $choices = BallotResponse::where('ballot_responses.question_id', $this->id)
//                    ->join('ballot_question_choices', 'ballot_question_choices.id', '=', 'ballot_responses.ballot_question_choice_id')
//                    ->groupBy('ballot_question_choices.id', 'ballot_question_choices.title')
//                    ->select('ballot_question_choices.title', DB::raw('COUNT(*) as count'))
//                    ->pluck('count', 'title')
//                    ->toArray();
//
//                $choicesWithCounts = array_map(function ($choice) use ($choices) {
//                    return [
//                        'title' => $choice,
//                        'count' => $choices[$choice] ?? 0,
//                    ];
//                }, $allChoices);
//
//                return $choicesWithCounts;
            }
        );
    }
}
