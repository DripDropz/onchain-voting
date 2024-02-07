<?php

use Illuminate\Support\HtmlString;
use Vinkla\Hashids\Facades\Hashids;

if (! function_exists('vite_production_assets')) {
    function vite_production_assets(): HtmlString
    {
        $manifest = json_decode(file_get_contents(
            public_path('build/manifest.json')
        ), true);

        return new HtmlString(<<<HTML
        <script type="module" src="/build/{$manifest['resources/js/app.ts']['file']}"></script>
        <link rel="stylesheet" href="/build/{$manifest['resources/js/app.ts']['css'][0]}">
    HTML);
    }
}

if (! function_exists('decode_model_hash')) {
    function decode_model_hash($hash, $model): ?int
    {
        return Hashids::connection($model)->decode($hash)[0] ?? null;
    }
}

if (!function_exists('compare_values')) {
    /**
     * compares $first number against $second using given operator.
     */
    function compare_values(int $first, int $second, $operator): ?bool
    {
        switch ($operator) {
            case ">=":
                return $first >= $second;
                break;
            case ">":
                return $first > $second;
                break;
            case '<=':
                return $first <= $second;
                break;
            case '<':
                return $first <= $second;
                break;
            case '=':
                return $first = $second;
                break;
        }    }
}

if (! function_exists('previous_route_name')) {
    function previous_route_name(): string
    {
        return app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
    }
}

if (! function_exists('previous_route_name_is')) {
    function previous_route_name_is(string $routeName): bool
    {
        return previous_route_name() === $routeName;
    }
}

function previous_route_name(): string
{
    return app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
}

function previous_route_url(): string
{
    $previousUrl = url()->previous();
    $currentUrl = url()->current();
    if ($previousUrl === $currentUrl) {
        return route('catalyst-explorer.proposals');
    }

    return app('router')->getRoutes()
        ->match(
            app('request')->create(url()->previous())
        )?->uri();
}


