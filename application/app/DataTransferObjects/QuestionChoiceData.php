<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionChoiceData extends Data
{
    public function __construct(
        public ?string $hash,

        #[Required, StringType]
        public string $title,

        #[TypescriptOptional]
        public ?string $description,

        #[TypescriptOptional]
        public ?bool $selected,

        #[TypeScriptOptional]
        public ?Carbon $created_at,

        #[FromRouteParameter('question')]
        public ?QuestionData $question,

        public ?BallotData $ballot,

        public ?int $order,
    ) {
    }
}
