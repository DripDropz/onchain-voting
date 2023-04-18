<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;

class TokenData extends Data
{
    public function __construct(
        public string $hash,

        public string $policyId,

        #[TypescriptOptional]
        public ?BallotData $ballot,

        public VoterData $voter,
    ) {}
}
