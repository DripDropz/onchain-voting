<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use
        HasTimestamps,
        SoftDeletes;

    protected $casts = [
        'script' => 'json',
    ];
}
