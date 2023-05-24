<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use \OwenIt\Auditing\Auditable as IsAuditable;
use Znck\Eloquent\Relations\BelongsToThrough;
use \Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class BallotQuestionChoice extends Model implements Auditable
{
    use BelongsToThroughTrait, HasHashIds, HashIdModel, IsAuditable;

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function ballot(): BelongsToThrough
    {
        return $this->belongsToThrough( Ballot::class, Question::class,'id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
