<?php

namespace App\Models;

use App\Enums\BallotTypeEnum;
use App\Enums\ModelStatusEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

class Ballot extends Model implements Auditable, HasUser
{
    use \OwenIt\Auditing\Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser;

    protected $fillable = [
        'title',
        'description',
        'version',
        'status',
        'type',
        'started_at',
        'ended_at',
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
        'live',
        'publishable',
    ];

    protected $casts = [
        'type' => BallotTypeEnum::class,
        'status' => ModelStatusEnum::class,
        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Scope a query to only include active users.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    public function live(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->started_at?->lte(Carbon::now()) && $this->status == 'published')
        );
    }

    public function choices(): HasManyThrough
    {
        return $this->hasManyThrough(BallotQuestionChoice::class, Question::class, 'ballot_id', 'question_id');
    }

    public function publishable(): Attribute
    {
        return Attribute::make(
            get: function () {
                $questions = Question::where('ballot_id', $this->id)->get();
                $ballotPulishable = $questions->flatMap(function ($question) {
                    if ($question->status = 'published' and ! is_null($this->started_at)) {
                        return $question->choices;
                    }
                });

                return $ballotPulishable->isEmpty() ? false : true;
            }
        );
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(BallotResponse::class);
    }

    public function snapshot(): HasOne
    {
        return $this->hasOne(Snapshot::class);
    }

    public function registration_policy(): HasOne
    {
        return $this->hasOne(Policy::class, 'model_id')->where(
            'context', 'registration'
        );
    }

    public function voting_policy(): HasOne
    {
        return $this->hasOne(Policy::class, 'model_id')->where(
            'context', 'voting'
        );
    }

    public function user_response(): HasOne
    {
        return $this->responses()->one()->ofMany()->where(
            'user_id', auth()?->user()?->getAuthIdentifier()
        );
    }

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class, 'model_id')
        ->where('model_type', static::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        //        static::addGlobalScope(new OrderByLiveBallotScope);
    }
}
