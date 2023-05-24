<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface HasUser
{
    public function user(): BelongsTo;

    public function userIdentifier(): Attribute;
}
