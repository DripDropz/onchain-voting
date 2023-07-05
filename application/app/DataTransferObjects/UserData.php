<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
// #[MapName(CamelCaseMapper::class)]
class UserData extends Data
{
    public function __construct(
        public string $hash,

        public string $name,

        public ?string $voter_id,

        #[TypescriptOptional]
        public ?string $hero,

        #[TypescriptOptional]
        #[DataCollectionOf(BallotData::class)]
        public ?array $ballots,
    ) {
    }
}
