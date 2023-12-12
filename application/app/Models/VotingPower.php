<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VotingPower extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasUser, HasHashIds, HashIdModel, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'snapshot_id',
        'voting_power',
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(Snapshot::class, 'snapshot_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
