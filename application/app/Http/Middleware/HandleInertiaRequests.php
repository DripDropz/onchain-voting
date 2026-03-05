<?php

namespace App\Http\Middleware;

use App\DataTransferObjects\UserData;
use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Handle the incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = parent::handle($request, $next);

        // Add cache-busting headers for admin routes to prevent stale data
        if ($request->is('admin/*') || $request->is('admin')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $isAdminRoute = $request->is('admin/*') || $request->is('admin');
        $hasAdminSession = auth('admin')->check();

        $user = $isAdminRoute
            ? auth('admin')?->user()
            : auth()?->user();

        $user?->load(['roles']);

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? UserData::from($user) : null,
            ],
            'adminContext' => [
                'isAdminRoute' => $isAdminRoute,
                'label' => $isAdminRoute ? 'Admin Console' : null,
                'hasAdminSession' => $hasAdminSession,
            ],
            'feature-flags' => [
                'ballots' => config('app.feature_flags.ballots'),
                'petitions' => config('app.feature_flags.petitions'),
                'polls' => config('app.feature_flags.polls'),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                    'uri' => $request->getRequestUri(),
                ]);
            },
        ]);
    }
}
