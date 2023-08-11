<?php

namespace App\DataTransferObjects;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class VerificationKey extends CardanoKey
{
    public function __construct() {}
}
