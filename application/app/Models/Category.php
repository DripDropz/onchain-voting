<?php

namespace App\Models;

use App\Models\Petition;
use App\Models\ModelCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Category extends Model
{
    use HasFactory;

    public function petitions():MorphToMany
    {
        return $this->morphedByMany(Petition::class, 'model', ModelCategory::class, 'category_id', 'model_id')
        ->withPivot(['model_type']);    
    }
}
