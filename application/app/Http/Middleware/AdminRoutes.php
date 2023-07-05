<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || empty($user->roles) || ! $this->hasAdminRole($user->roles)) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }

    private function hasAdminRole($roles)
    {
        foreach ($roles as $role) {
            if ($role->name === 'admin' || $role->name === 'super-admin') {
                return true;
            }
        }

        return false;
    }
}
