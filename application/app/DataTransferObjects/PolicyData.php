<?php

namespace App\DataTransferObjects;

use Doctrine\DBAL\Types\JsonType;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;


#[TypeScript]
class PolicyData extends Data
{
    public function __construct(
        #[WithoutValidation]
        public ?string $hash,

        #[Required]
        public array $script,

        public ?string $policy_id,

        #[Required]
        public ?string $context,

        #[TypeScriptOptional]
        public ?string $created_at,

        #[TypeScriptOptional]
        public ?string $image_link,
    ) {}
}
