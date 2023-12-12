<?php

namespace App\Models;

use App\Enums\PolicyTypeEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use
        HasTimestamps,
        HasFactory,
        SoftDeletes;

    protected $casts = [
        'script' => 'json',
        'context' => PolicyTypeEnum::class,

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
