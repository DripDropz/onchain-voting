<?php

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
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
        #[StringType, Max(1200)]
        public ?string $description,

        #[TypescriptOptional]
        #[Required, Numeric, Max(200)]
        public ?string $version,

        #[StringType]
        public ?string $status,

        #[BooleanType]
        public ?bool $live,

        #[BooleanType]
        public ?bool $open,

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

        /** @var QuestionData[] */
        public ?DataCollection $questions,

        /** @var PolicyData[] */
        public ?DataCollection $policies,

        #[TypescriptOptional]
        #[DataCollectionOf(VoterData::class)]
        /** @var VoterData[] */
        public ?DataCollection $voters,

        #[TypescriptOptional]
        #[DataCollectionOf(BallotResponseData::class)]
        public ?DataCollection $responses,

        #[TypescriptOptional]
        #[DataCollectionOf(BallotResponseData::class)]
        /** @var BallotResponseData[] */
        public ?DataCollection $user_responses,

        #[TypescriptOptional]
        #[DataCollectionOf(VoteData::class)]
        /** @var VoteData[] */
        public ?DataCollection $votes,

        #[TypescriptOptional]
        #[DataCollectionOf(TokenData::class)]
        /** @var TokenData[] */
        public ?DataCollection $tokens,

        #[TypescriptOptional]
        #[DataCollectionOf(TxData::class)]
        /** @var TxData[] */
        public ?DataCollection $txs,
    ) {
    }

    public static function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
        ];
    }
    public static function rules(): array
    {
        return [
            'policies' => 'array|max:2',
            
        ];
    }
}
