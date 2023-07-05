<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Vinkla\Hashids\Facades\Hashids;

trait HashIdModel
{
    public function hash(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Hashids::connection(static::class)->encode($this->id)
        );
    }

    public function rawId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getRawOriginal('id')
        );
    }
}
