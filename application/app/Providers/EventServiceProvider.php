<?php

namespace App\Providers;

use App\Events\votingPowersImportedEvent;
use App\Models\Policy;
use App\Models\Snapshot;
use App\Listeners\PublishSnapshotListener;
use App\Observers\SnapshotObserver;
use App\Observers\PolicyObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        votingPowersImportedEvent::class => [
            PublishSnapshotListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Snapshot::observe(SnapshotObserver::class);
        Policy::observe(PolicyObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
