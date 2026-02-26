<?php

namespace App\Models;

use App\Enums\ModelStatusEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Poll extends Model implements Auditable, HasMedia, HasUser
{
    use HasFactory,
        HasHashIds,
        HashIdModel,
        InteractsWithMedia,
        \OwenIt\Auditing\Auditable,
        Traits\HasUser;

    protected $hidden = [
        'id',
    ];

    protected $withCount = [
        'responses',
    ];

    protected $appends = [
        'hash',
        'image_url',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'type',
        'publish_on_chain',
        'visibility',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'status' => ModelStatusEnum::class,
        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function snapshot(): HasOne
    {
        return $this->hasOne(Snapshot::class);
    }

    public function user_responses(): HasMany
    {
        return $this->responses()->where(
            'user_id',
            auth()?->user()?->getAuthIdentifier()
        );
    }

    public function question(): HasOne
    {
        return $this->hasOne(Question::class, 'model_id')
            ->where('model_type', Poll::class);
    }

    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class, 'poll_rule');
    }

    public function choices(): HasManyThrough
    {
        return $this->hasManyThrough(
            QuestionChoice::class,
            Question::class,
        );
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionResponse::class, 'model_id')
            ->where('model_type', static::class);
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMediaUrl('polls') ?: null,
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('polls')
            ->singleFile()
            ->useDisk('public');
    }
}
