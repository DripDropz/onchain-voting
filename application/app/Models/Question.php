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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        'model_type',
        'started_at',
        'user_id'
    ];

    protected $hidden = [
        'id',
        'model_id',
    ];

    protected $appends = [
        'hash',
        'choices_tally'
    ];

    protected $casts = [
        'type' => QuestionTypeEnum::class,
        'status' => ModelStatusEnum::class,
        'created_at' => 'datetime:Y-m-d H:i:s',
        'choices' => QuestionChoiceData::class,
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class, 'model_id');
    }

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(QuestionChoice::class, 'question_id');
    }

    public function ranked_user_responses(): HasMany
    {
        return $this->hasMany(QuestionResponse::class)->where(
            [
                'user_id' => auth()?->user()?->getAuthIdentifier(),
            ]
        )->whereNotNull('rank')->orderBy('rank', 'asc');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionResponse::class, 'model_id');
    }

    public function choicesTally(): Attribute
    {
        return Attribute::make(
            get: function () {
                $allChoices = $this->choices()
                    ->pluck('title')
                    ->toArray();

                $query = QuestionResponse::where('question_id', $this->id);

                foreach ($allChoices as $choice) {
                    $c =  Str::snake(strtolower(trim($choice, '.')));
                    $query->withCount(
                        [
                            "choices as {$c}_count" => function ($query) use ($choice) {
                                $query->where('question_choices.title', "$choice");
                            }
                        ]
                    );
                }
                $attributes = collect($query->first()?->attributes);

                return $attributes
                    ->filter(
                        fn($att, $key) => Str::of($key)->contains('count'))
                    ->map(function($tally, $key) {
                        return [
                            'title' => Str::of($key)
                                ->replace(['_count', '_'], ' ')
                                ->trim()
                                ->title(),
                            'count' => $tally,
                        ];
                    })->values()
                    ->toArray();
            }
        );
    }
}
