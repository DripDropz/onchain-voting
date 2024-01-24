<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ModelSignature extends MorphPivot
{
    protected $table = 'model_signatures';
}
