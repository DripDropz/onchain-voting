<?php

namespace App\Models;

use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Question extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasHashIds, HashIdModel;

    protected $fillable = [
        'title',
        'description',
        'supplemental',
        'max_choices',
        'status',
        'type',
        'started_at'
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
    ];

    protected $casts = [
        'type' => QuestionTypeEnum::class,
        'status' => ModelStatusEnum::class,
        'started_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }
}
