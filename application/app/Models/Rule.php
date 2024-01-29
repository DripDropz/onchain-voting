<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rule extends Model
{
    use HasFactory, HasHashIds, HashIdModel, SoftDeletes;

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
    ];

    protected $quarded = [];
    
    public function petitions(): BelongsToMany
    {
        return $this->belongsToMany(Petition::class);
    }
}
