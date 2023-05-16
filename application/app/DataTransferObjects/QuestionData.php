<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(CamelCaseMapper::class)]
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

        #[DataCollectionOf(QuestionChoiceData::class)]
        public ?array $choices,
    ) {}
}
