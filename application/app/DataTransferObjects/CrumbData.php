<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\LaravelData\Attributes\WithoutValidation;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;

#[TypeScript]
class CrumbData extends Data
{
    public function __construct(
        #[Required, StringType]
        public ?string $label,

        #[Required, StringType]
        public ?string $link,

        #[TypescriptOptional]
        public ?bool $external,

    ) {
    }
}
