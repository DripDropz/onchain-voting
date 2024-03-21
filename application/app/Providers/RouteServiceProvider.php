<?php

namespace App\Providers;

use App\Models\Poll;
use App\Models\Rule;
use App\Models\Ballot;
use App\Models\Policy;
use App\Models\Petition;
use App\Models\Question;
use App\Models\Signature;
use App\Models\Snapshot;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const DASHBOARD = '/admin/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Route::bind('ballot', function ($value, $route) {
            return $this->getModel(Ballot::class, $value);
        });

        Route::bind('petition', function ($value, $route) {
            return $this->getModel(Petition::class, $value);
        });

        Route::bind('poll', function ($value, $route) {
            return $this->getModel(Poll::class, $value);
        });

        Route::bind('question', function ($value, $route) {
            return $this->getModel(Question::class, $value);
        });

        Route::bind('snapshot', function ($value, $route) {
            return $this->getModel(Snapshot::class, $value);
        });

        Route::bind('policy', function ($value, $route) {
            return $this->getModel(Policy::class, $value);
        });

        Route::bind('rule', function ($value, $route) {
            return $this->getModel(Rule::class, $value);
        });

        Route::bind('signature', function ($value, $route) {
            return $this->getModel(Signature::class, $value);
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    private function getModel($model, $routeKey)
    {
        $id = Hashids::connection($model)->decode($routeKey)[0] ?? null;
        $modelInstance = resolve($model);

        return $modelInstance->findOrFail($id);
    }
}
