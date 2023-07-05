<?php

namespace App\DataTransferObjects\Normalizers;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\LaravelData\Normalizers\Normalizer;

class ArrayableNormalizer implements Normalizer
{
    public function normalize(mixed $value): array
    {
        if (! $value instanceof Arrayable) {
            return [];
        }

        return $value->toArray();
    }
}
