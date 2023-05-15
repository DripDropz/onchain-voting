<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
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
        #[MapOutputName('max_choices')]
        public int $maxChoices,

        #[Required]
        public string $status,

        #[Rule('string')]
        public string $type,

        public ?UserData $user,

        #[FromRouteParameter('ballot')]
        public ?BallotData $ballot,

        #[DataCollectionOf(QuestionChoicesData::class)]
        public ?array $choices,
    ) {}
}
