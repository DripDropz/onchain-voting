<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuestionChoicesData extends Data
{
    public function __construct(
        public string       $hash,

        public QuestionData $question,
    )
    {}
}
