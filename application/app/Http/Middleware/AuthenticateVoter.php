<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\VotingPower;
use App\Models\Wallet;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateVoter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user() ?? null;
        
        if ( is_null($user) ) {
            $ballot = $request->route()->parameter('ballot');

            if (!!$ballot){
                return redirect()->route('login.wallet', ['hash' => $ballot->hash]);
            } else {
                return redirect()->route('login.wallet');
            }
                
        } else {
            return $next($request);
        }

    }
}
