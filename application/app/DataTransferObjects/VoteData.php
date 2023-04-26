<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VoteData extends Data
{
    public function __construct(
        public string $hash,

        public ?int $power,

        public ?TokenData $token,

        public VoterData $voter,

        public ?BallotData $ballot
    ) {}
}
