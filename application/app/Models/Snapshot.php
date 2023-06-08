<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Snapshot extends Model implements Auditable, HasUser
{
    use \OwenIt\Auditing\Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser;

    protected $fillable = [
        'title',
        'description',
        'policy_id',
        'ballot_id',
        'status',
        'type'
    ];

    protected $appends = [
        'hash',
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function voting_powers()
    {
        return $this->hasMany(VotingPower::class);
    }
}
