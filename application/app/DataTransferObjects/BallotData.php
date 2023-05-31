<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BallotData extends Data
{
    public function __construct(
        #[WithoutValidation]
        public ?string $hash,

        #[Required, StringType]
        public string $title,

        #[TypescriptOptional]
        #[StringType, Max(400)]
        public ?string $description,

        #[TypescriptOptional]
        #[Required, IntegerType, Max(200)]
        public ?string $version,

        #[StringType]
        public ?string $status,

        #[Rule('string')]
        public ?string $type,

        #[TypeScriptOptional]
        #[MapOutputName('total_votes')]
        public mixed $totalVotes,

        #[TypeScriptOptional]
        public ?SnapshotData $snapshot,

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

    public static function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
        ];
    }
}
