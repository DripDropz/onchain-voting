<?php

namespace App\Casts;

use App\DataTransferObjects\VerificationKey;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class VerificationKeyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {

        return VerificationKey::from($attributes);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value instanceof VerificationKey) {
            throw new InvalidArgumentException('The given value is not an Address instance.');
        }

        return $value?->toArray() ?? [];
    }
}
