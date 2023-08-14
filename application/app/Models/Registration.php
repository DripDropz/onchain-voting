<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory;

    public $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class, 'ballot_id');
    }

    public function voting_power()
    {
        return $this->belongsTo(VotingPower::class, 'voting_power_id');
    }
}
