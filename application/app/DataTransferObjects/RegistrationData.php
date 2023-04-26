<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional as TypescriptOptional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RegistrationData extends Data
{
    public function __construct(
        public string $hash,

        public int $power,

        #[TypescriptOptional]
        public ?TokenData $token,
    ) {}
}
