<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BallotData extends Data
{
    public function __construct(
        public string $hash,

        public string $title,

        #[TypescriptOptional]
        public ?string $description,

        #[TypescriptOptional]
        public ?string $version,

        public string $status,

        public string $type,

        #[TypeScriptOptional]
        #[MapOutputName('total_votes')]
        public mixed $totalVotes,

        public ?UserData $user,

        #[TypescriptOptional]
        #[DataCollectionOf(QuestionData::class)]
        public ?array $questions,

        #[TypescriptOptional]
        #[DataCollectionOf(VoterData::class)]
        public ?array $voters,

        #[TypescriptOptional]
        #[DataCollectionOf(VoteData::class)]
        public ?array $votes,

        #[TypescriptOptional]
        #[DataCollectionOf(TokenData::class)]
        public ?array $tokens,

        #[TypescriptOptional]
        #[DataCollectionOf(TxData::class)]
        public ?array $txs,
    ) {}
}
