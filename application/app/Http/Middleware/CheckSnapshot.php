<?php

namespace App\Http\Middleware;

use App\Models\Ballot;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSnapshot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ballot = $request->route()->parameter('ballot');
        if ($ballot && !$ballot->snapshot) {

            return redirect()->route('ballot.missing.snapshot', ['ballot' => $ballot]);
        }
        return $next($request);
    }
}
