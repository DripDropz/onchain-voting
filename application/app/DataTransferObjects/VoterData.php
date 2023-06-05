<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(CamelCaseMapper::class)]
class VoterData extends Data
{
    public function __construct(
        public string $voter_id,

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
