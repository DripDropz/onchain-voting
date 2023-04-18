<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class QuestionChoicesData extends Data
{
    public function __construct(
        public string       $hash,

        public QuestionData $question,
    )
    {}
}
