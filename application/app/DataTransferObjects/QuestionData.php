<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionData extends Data
{
    public function __construct(
        public string $hash,

        #[DataCollectionOf(QuestionChoicesData::class)]
        public array $choices,
    ) {}
}
