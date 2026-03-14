<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RuleData extends Data
{
    public function __construct(
        #[WithoutValidation]
        public ?string $hash,

        #[Required, StringType]
        public string $title,

        #[TypescriptOptional]
        #[StringType]
        public ?string $description,

        #[StringType]
        public ?string $type,

        #[StringType]
        public ?string $operator,

        #[StringType]
        public ?string $value1,

        #[StringType]
        public ?string $value2,

        #[TypescriptOptional]
        #[StringType]
        public ?string $image_url,

        #[TypescriptOptional]
        #[StringType]
        public ?string $asset_metadata,
    ) {}
}
