<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('hashed_exists', function ($attribute, $value, $parameters, $validator) {
            if (is_array($value)) {
                $value = array_map(fn($item) => (
                Hashids::connection('App\\Models\\' . Str::studly(Str::singular($parameters[0])))->decode($item)
                ), $value);
            } else {
                $value = Hashids::connection('App\\Models\\' . Str::studly(Str::singular($parameters[0])))->decode($value);
            }

            // Delegate to `exists:` validator
            return $validator->validateExists($attribute, $value, $parameters);
        });

    }
}
