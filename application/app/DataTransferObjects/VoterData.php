<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;

class VoterData extends Data
{
    public function __construct(
        public string $hash,

        public string $stakeKey,

        #[TypescriptOptional]
        public ?string $votePower,

        #[TypescriptOptional]
        #[DataCollectionOf(VoteData::class)]
        public ?array $votes,

        #[TypescriptOptional]
        #[DataCollectionOf(RegistrationData::class)]
        public ?array $registrations,

        #[TypescriptOptional]
        #[DataCollectionOf(TokenData::class)]
        public ?array $tokens,

        #[TypescriptOptional]
        #[DataCollectionOf(TxData::class)]
        public ?array $txs,
    )
    {}
}
