<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Transformers\ArrayableTransformer;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionData extends Data
{
    public function __construct(
        public ?string $hash,

        #[Required, StringType]
        public string $title,

        #[TypescriptOptional]
        public ?string $description,

        #[TypescriptOptional]
        public ?string $supplemental,

        #[TypeScriptOptional]
        public ?int $max_choices,

        #[TypeScriptOptional]
        public ?Carbon $created_at,

        #[Required]
        public string $status,

        #[Rule('string')]
        public string $type,

        public ?UserData $user,

        #[FromRouteParameter('ballot')]
        public ?BallotData $ballot,

        // #[WithCastable()]
        #[DataCollectionOf(QuestionChoiceData::class)]
        #[WithTransformer(ArrayableTransformer::class)]
        /** @var QuestionChoiceData[] */
        public ?DataCollection $choices,

        public ?array $choices_tally,
    ) {
    }
}
