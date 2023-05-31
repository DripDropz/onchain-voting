<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SnapshotData extends Data
{
    public function __construct(
<<<<<<< Updated upstream
      //
=======
        #[WithoutValidation]
        public ?string $hash,

        #[Required, StringType]
        public string $title,

        #[TypescriptOptional]
        #[Rule('string')]
        public ?string $description,

        #[WithoutValidation]
        public ?UserData $user,

        #[TypeScriptOptional]
        public ?BallotData $ballot,

        #[TypeScriptOptional]
        public ?string $created_at,

        #[TypeScriptOptional]
        public ?string $updated_at,

        #[TypescriptOptional]
        public ?string $policy_id,

        #[TypescriptOptional]
        #[Rule('string')]
        public ?string $type,

        #[Required]
        #[Rule('string')]
        public string $status
>>>>>>> Stashed changes
    ) {}
}
