<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use App\Models\Interfaces\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;


class VotingPower extends Model implements Auditable,HasUser
{
    use  HasHashIds, HashIdModel, \OwenIt\Auditing\Auditable, Traits\HasUser,HasFactory;
    

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
