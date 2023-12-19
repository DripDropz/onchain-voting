<?php

namespace App\Models;

use App\Enums\PolicyTypeEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

class Policy extends Model
{
    use
        HasTimestamps,
        HasHashIds,
        HashIdModel,
        SoftDeletes;

    protected $casts = [
        'script' => 'json',
        'context' => PolicyTypeEnum::class,

    ];

    protected $appends = [
        'hash',
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'context_id')
        ->where('context_type', self::class);
    }
}
