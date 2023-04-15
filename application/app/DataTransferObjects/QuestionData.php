<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{
    public function __construct(
        public string $hash,

        #[DataCollectionOf(QuestionChoicesData::class)]
        public array $choices,
    ) {}
}
