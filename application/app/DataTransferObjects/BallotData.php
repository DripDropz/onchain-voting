<?php

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
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

        #[BooleanType]
        public ?bool $live,

        #[Rule('string')]
        public ?string $type,

        #[TypeScriptOptional]
        public ?string $created_at,

        #[TypeScriptOptional]
        public ?string $updated_at,

        #[TypeScriptOptional]
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class)]
        public ?CarbonImmutable $started_at,

        #[TypeScriptOptional]
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class)]
        public ?CarbonImmutable $ended_at,

        #[TypeScriptOptional]
        public mixed $total_votes,

        #[TypeScriptOptional]
        public ?SnapshotData $snapshot,

        public ?UserData $user,

        #[TypescriptOptional]
        /** @var QuestionData[] */
        public ?DataCollection $questions,

        #[TypescriptOptional]
        #[DataCollectionOf(VoterData::class)]
        /** @var QuestionData[] */
        public ?DataCollection $voters,

        #[TypescriptOptional]
        #[DataCollectionOf(BallotResponseData::class)]
        public ?DataCollection $responses,

        #[TypescriptOptional]
        public ?BallotResponseData $user_response,

        #[TypescriptOptional]
        #[DataCollectionOf(VoteData::class)]
        /** @var QuestionData[] */
        public ?DataCollection $votes,

        #[TypescriptOptional]
        #[DataCollectionOf(TokenData::class)]
        /** @var QuestionData[] */
        public ?DataCollection $tokens,

        #[TypescriptOptional]
        #[DataCollectionOf(TxData::class)]
        /** @var QuestionData[] */
        public ?DataCollection $txs,
    ) {}

    public static function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description'
        ];
    }
}
