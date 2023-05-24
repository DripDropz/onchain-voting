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
        public string $hash,

        public string $stake_key,

        #[TypescriptOptional]
        public ?string $vote_power,

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
