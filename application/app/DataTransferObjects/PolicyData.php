<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Doctrine\DBAL\Types\JsonType;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;


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

        #[IntegerType]
        public ?int $wallet_balance,

        #[BooleanType]
        public ?bool $wallet_funded,


    ) {}
}
