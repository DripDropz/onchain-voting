<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(CamelCaseMapper::class)]
class TokenData extends Data
{
    public function __construct(
        public string $hash,

        public string $policy_id,

        #[TypescriptOptional]
        public ?BallotData $ballot,

        public VoterData $voter,
    ) {
    }
}
