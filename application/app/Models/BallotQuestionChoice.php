<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as IsAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class BallotQuestionChoice extends Model implements Auditable
{
    use BelongsToThroughTrait, HasHashIds, HashIdModel, IsAuditable;

    protected $fillable = [
        'title',
        'description',
        'status',
        'order',
    ];

    protected $hidden = [
        'id',
        'question_id',
    ];

    protected $appends = [
        'hash',
        'question_hash',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    const ORDER_GAP = 60000;

    public function questionHash(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->question?->hash
        );
    }

    public function ballot(): BelongsToThrough
    {
        return $this->belongsToThrough(Ballot::class, Question::class, 'id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public static function booted(): void
    {
        static::creating(function ($model) {
            $model->order = self::query()->where('question_id', $model->question_id)
                    ->orderByDesc('order')->first()?->order + self::ORDER_GAP;
        });

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
