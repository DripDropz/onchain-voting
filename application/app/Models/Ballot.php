<?php

namespace App\Models;

use App\Enums\BallotTypeEnum;
use App\Enums\ModelStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'open',
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
            get: fn() => ($this->started_at?->lte(Carbon::now()) && $this->status == 'published')
        );
    }

    public function open(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::now()->lte($this->ended_at)
        );
    }

    public function choices(): HasManyThrough
    {
        return $this->hasManyThrough(
            QuestionChoice::class,
            Question::class,
            'model_id',
            'question_id');
    }

    public function publishable(): Attribute
    {
        return Attribute::make(
            get: function () {
                $questions = Question::where(['model_id'=>$this->id,'model_type'=>static::class])->get();
                $ballotPulishable = $questions->flatMap(function ($question) {
                    if ($question->status = 'published' and !is_null($this->started_at)) {
                        return $question->choices;
                    }
                });

                return !$ballotPulishable->isEmpty();
            }
        );
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class,'model_id')
            ->where('model_type', static::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionResponse::class, 'model_id')
            ->where('model_type', static::class);
    }

    public function user_responses(): HasMany
    {
        return $this->responses()->where(
            'user_id',
            auth()?->user()?->getAuthIdentifier()
        );
    }

    public function snapshot(): HasOne
    {
        return $this->hasOne(Snapshot::class);
    }

    public function registration_policy(): HasOne
    {
        return $this->hasOne(Policy::class, 'model_id')
            ->where(
                'context',
                'registration'
            );
    }

    public function voting_policy(): HasOne
    {
        return $this->hasOne(Policy::class, 'model_id')
            ->where(
                'context',
                'voting'
            );
    }

    public function petition(): HasMany
    {
        return $this->hasMany(Petition::class, 'ballot_id');
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
