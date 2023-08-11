<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VotingPowerData extends Data
{
    public function __construct(
        #[WithoutValidation]
        public ?string $hash,

        #[DataCollectionOf(UserData::class)]
        public ?UserData $user,

        public ?SnapshotData $snapshot,

        #[Required, IntegerType]
        public int $voting_power,

        #[TypeScriptOptional]
        public ?string $created_at,
    ) {
    }
}
