<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $features): Response
    {

        $featuresArray = explode(',', $features);

        foreach ($featuresArray as $feature) {
            if ($feature === 'ballot' && env('FEATURE_BALLOT') === false) {
                return abort(404);
            }

            if ($feature === 'petition' && env('FEATURE_PETITION') === false) {
                return abort(404);
            }

            if ($feature === 'poll' && env('FEATURE_POLL') === false) {
                return abort(404);
            }
        }

        return $next($request);
    }
}
