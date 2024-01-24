<?php

namespace App\Models\Traits;

use App\Models\Category;
use App\Models\ModelCategory;
use App\Models\ModelSignature;
use App\Models\Signature;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasSignatures
{
    public function signatures(): BelongsToMany
    {
        return $this->belongsToMany(Signature::class, ModelSignature::class, 'model_id', 'signature_id')
            ->where('model_type', static::class)
            ->withPivot('model_type');
    }


}
