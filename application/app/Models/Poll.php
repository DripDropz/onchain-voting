<?php

namespace App\Models;

use App\Enums\ModelStatusEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Poll extends Model implements Auditable, HasUser
{
    use \OwenIt\Auditing\Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser,
        HasFactory;

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'type',
        'on_chain',
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
}
