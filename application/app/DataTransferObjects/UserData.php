<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;

class UserData extends Data
{
    public function __construct(
        public string $hash,

        public string $name,

        #[TypescriptOptional]
        public ?string $hero,

        #[TypescriptOptional]
        #[DataCollectionOf(BallotData::class)]
        public array  $ballots,
    )
    {}
}
