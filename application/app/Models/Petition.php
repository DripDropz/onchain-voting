<?php

namespace App\Models;

use App\Enums\RuleV1Enum;
use App\Enums\ModelStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\Traits\HasHashIds;
use App\Models\Interfaces\HasUser;
use App\Models\Traits\HashIdModel;
use App\Models\Traits\HasSignatures;
use App\Models\Traits\HasTaxonomies;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Petition extends Model implements Auditable, HasUser
{
    use \OwenIt\Auditing\Auditable,
        HasHashIds,
        HashIdModel,
        Traits\HasUser,
        HasSignatures,
        HasTaxonomies;

    protected $withCount = [
        'signatures'
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = [
        'hash', 'closed','petition_goals'
    ];

    protected $casts = [
        'status' => ModelStatusEnum::class,
        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $guarded = [];

    public function snapshot(): HasOne
    {
        return $this->hasOne(Snapshot::class);
    }

    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class);
    }

    public function closed(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->ended_at?->lte(Carbon::now()))
        );
    }

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class, 'ballot_id');
    }

    public function petitionGoals(): Attribute
    {
        return Attribute::make(
            get: function(){
                $goals = collect([]);

                $this->rules()->each(
                    function ($r) use($goals) {
                        if ($r->value1 == RuleV1Enum::BALLOT_ELIGIBLE->value) {
                            $goals[RuleV1Enum::BALLOT_ELIGIBLE->value] = $r->only('type', 'operator','value1','value2');
                        }elseif ($r->value1 == RuleV1Enum::FEATURE_PETITION->value) {
                            $goals[RuleV1Enum::FEATURE_PETITION->value] = $r->only('type', 'operator', 'value1', 'value2');
                        }elseif ($r->value1 == RuleV1Enum::VISIBLE->value) {
                            $goals[RuleV1Enum::VISIBLE->value] = $r->only('type', 'operator', 'value1', 'value2');
                        }
                    }
                );

                return $goals;

            }
        );
    }
}
