<?php

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
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
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(CamelCaseMapper::class)]
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
        public ?string $createdAt,

        #[TypeScriptOptional]
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class)]
        public ?CarbonImmutable $started_at,

        #[TypeScriptOptional]
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class)]
        public ?CarbonImmutable $ended_at,

        #[TypeScriptOptional]
        public mixed $totalVotes,

        public ?UserData $user,

        #[TypescriptOptional]
        #[DataCollectionOf(QuestionData::class)]
        public $questions,

        #[TypescriptOptional]
        #[DataCollectionOf(VoterData::class)]
        public ?array $voters,

        #[TypescriptOptional]
        #[DataCollectionOf(VoteData::class)]
        public $votes,

        #[TypescriptOptional]
        #[DataCollectionOf(TokenData::class)]
        public $tokens,

        #[TypescriptOptional]
        #[DataCollectionOf(TxData::class)]
        public $txs,
    ) {}

    public static function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
        ];
    }
}
