<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;

class BallotData extends Data
{
    public function __construct(
        public string $hash,

        public UserData $user,

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
