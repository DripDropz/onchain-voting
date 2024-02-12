<?php

namespace App\Models;

use App\Enums\PolicyTypeEnum;
use App\Http\Integrations\Lucid\LucidConnector;
use App\Http\Integrations\Lucid\Requests\GetBalance;
use App\Http\Traits\HasHashIds;
use App\Models\Traits\HashIdModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Policy extends Model
{
    use
        HasTimestamps,
        HasHashIds,
        HashIdModel,
        SoftDeletes;

    protected $casts = [
        'script' => 'json',
        'context' => PolicyTypeEnum::class,

    ];

    protected $appends = [
        'hash',
        'wallet_balance',
        'wallet_funded'
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'context_id')
            ->where('context_type', self::class);
    }

    public function walletBalance(): Attribute
    {
        return Attribute::make(
            get: function () {
                $seed = $this->wallet->passphrase;
                $walletBalance = new GetBalance;
                $walletBalance->body()->merge([
                    'seed' => $seed
                ]);
                $lucid = new LucidConnector;
                $walletResponse = $lucid->send($walletBalance);
                $balance = $walletResponse->body();
                return is_numeric($balance) ? (float)$balance : 0;
            }
        );
    }

    public function walletFunded(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->walletBalance > 5;
            }
        );
    }
}
