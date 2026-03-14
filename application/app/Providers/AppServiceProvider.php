<?php

namespace App\Providers;

use App\Events\PetitionSigned;
use App\Events\votingPowersImportedEvent;
use App\Http\Controllers\SignedStorageUrlController;
use App\Listeners\PublishSnapshotListener;
use App\Listeners\UpdatePetitionAfterSignature;
use App\Models\Ballot;
use App\Models\Policy;
use App\Models\Poll;
use App\Models\Petition;
use App\Models\Question;
use App\Models\Rule;
use App\Models\Signature;
use App\Models\Snapshot;
use App\Observers\BallotObserver;
use App\Observers\PolicyObserver;
use App\Observers\SnapshotObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Vapor\Contracts\SignedStorageUrlController as SignedStorageUrlControllerContract;
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
        $this->bootValidators();
        $this->bootVaporStorageUrl();
        $this->bootRouteBindings();
        $this->bootRateLimiting();
        $this->bootObservers();
        $this->bootEventListeners();
    }

    private function bootValidators(): void
    {
        Validator::extend('hashed_exists', function ($attribute, $value, $parameters, $validator) {
            if (is_array($value)) {
                $value = array_map(fn ($item) => (
                    Hashids::connection('App\\Models\\'.Str::studly(Str::singular($parameters[0])))->decode($item)
                ), $value);
            } else {
                $value = Hashids::connection('App\\Models\\'.Str::studly(Str::singular($parameters[0])))->decode($value);
            }

            return $validator->validateExists($attribute, $value, $parameters);
        });
    }

    private function bootVaporStorageUrl(): void
    {
        if (!app()->environment('production')) {
            $this->app->singleton(
                SignedStorageUrlControllerContract::class,
                SignedStorageUrlController::class
            );
        }
    }

    private function bootRouteBindings(): void
    {
        Route::bind('ballot', fn ($value) => $this->resolveHashidModel(Ballot::class, $value));
        Route::bind('petition', fn ($value) => $this->resolveHashidModel(Petition::class, $value));
        Route::bind('poll', fn ($value) => $this->resolveHashidModel(Poll::class, $value));
        Route::bind('question', fn ($value) => $this->resolveHashidModel(Question::class, $value));
        Route::bind('snapshot', fn ($value) => $this->resolveHashidModel(Snapshot::class, $value));
        Route::bind('policy', fn ($value) => $this->resolveHashidModel(Policy::class, $value));
        Route::bind('rule', fn ($value) => $this->resolveHashidModel(Rule::class, $value));
        Route::bind('signature', fn ($value) => $this->resolveHashidModel(Signature::class, $value));
    }

    private function bootRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    private function bootObservers(): void
    {
        Snapshot::observe(SnapshotObserver::class);
        Policy::observe(PolicyObserver::class);
        Ballot::observe(BallotObserver::class);
    }

    private function bootEventListeners(): void
    {
        Event::listen(Registered::class, SendEmailVerificationNotification::class);
        Event::listen(votingPowersImportedEvent::class, PublishSnapshotListener::class);
        Event::listen(PetitionSigned::class, UpdatePetitionAfterSignature::class);
    }

    private function resolveHashidModel(string $model, mixed $routeKey): mixed
    {
        $id = Hashids::connection($model)->decode($routeKey)[0] ?? null;

        return resolve($model)->findOrFail($id);
    }
}
