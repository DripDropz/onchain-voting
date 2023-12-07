<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'type',
        'metadata'
    ];

    protected $with = [
        'ballot',
    ];

    protected $appends = [
        'hash',
        'has_voting_powers',
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function voting_powers()
    {
        return $this->hasMany(VotingPower::class, 'snapshot_id');
    }

    public function hasVotingPowers(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->voting_powers()->count() > 0 ? true : false
        );
    }
}
