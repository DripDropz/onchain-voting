<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class AccountData extends Data
{
    public function __construct(
      public string $stakeKey,
      public string $votePower,
    ) {}
}
