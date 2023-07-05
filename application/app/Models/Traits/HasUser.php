<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUser
{
    public function userIdentifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->user->id
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
