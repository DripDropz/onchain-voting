<?php

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
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
