<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BallotResponse extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser,
        SoftDeletes;

    protected $hidden = [
        'id',
    ];

    protected $fillable = [
        'ballot_id',
        'ballot_question_choice_id',
        'question_id',
        'user_id',
        'voting_power_id',
        'submit_tx',
        'rank'
    ];

    public function voting_power()
    {
        return $this->belongsTo(VotingPower::class);
    }

    public function ballot()
    {
        return $this->belongsTo(Ballot::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function choice()
    {
        return $this->belongsTo(BallotQuestionChoice::class, 'ballot_question_choice_id');
    }
}
