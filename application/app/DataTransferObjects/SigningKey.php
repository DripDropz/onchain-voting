<?php

namespace App\DataTransferObjects;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SigningKey extends CardanoKey
{
    public function __construct() {}
}
