<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ModelCategory extends MorphPivot
{
    protected $table = 'model_categories';
}
