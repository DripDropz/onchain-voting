<?php

namespace App\Models;

use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use App\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;

class BallotResponse extends Model
{
    use \OwenIt\Auditing\Auditable,
    HasHashIds,
    HashIdModel,
    Traits\HasUser;

    protected $hidden = [
        'id',
    ];

    protected $fillable = [
        'ballot_id',
        'ballot_question_choice_id',
        'question_id',
        'user_id',
        'voting_power_id',
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
        return $this->belongsTo(QuestionChoice::class, 'ballot_question_choice_id');
    }

}
