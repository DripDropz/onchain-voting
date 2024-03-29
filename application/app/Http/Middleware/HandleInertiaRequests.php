<?php

namespace App\Http\Middleware;

use App\DataTransferObjects\UserData;
use Illuminate\Http\Request;
use Inertia\Middleware;
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
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
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
        $user = auth()?->user();

        $user?->load(['roles']);
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? UserData::from($user) : null,
            ],
            'feature-flags'=>[
                'ballots'=> config('app.feature_flags.ballots'),
                'petitions'=> config('app.feature_flags.petitions'),
                'polls'=> config('app.feature_flags.polls')
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
