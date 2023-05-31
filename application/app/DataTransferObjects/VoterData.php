<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VoterData extends Data
{
    public function __construct(
<<<<<<< Updated upstream
        public string $hash,

        public string $stakeKey,

        #[TypescriptOptional]
        public ?string $votePower,
=======
        public string $voter_id,
>>>>>>> Stashed changes

        #[TypescriptOptional]
        #[DataCollectionOf(VoteData::class)]
        public ?VoteData $votes,

        #[TypescriptOptional]
        #[DataCollectionOf(RegistrationData::class)]
        public ?RegistrationData $registrations,

        #[TypescriptOptional]
        #[DataCollectionOf(TokenData::class)]
        public ?TokenData $tokens,

        #[TypescriptOptional]
        #[DataCollectionOf(TxData::class)]
        public ?TxData $txs,
    )
    {}
}
