<?php

namespace App\Listeners;

use App\Enums\ModelStatusEnum;
use App\Events\votingPowersImportedEvent;
use App\Models\Snapshot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishSnapshotListener
{

    /**
     * Handle the event.
     */
    public function handle(votingPowersImportedEvent $event): void
    {
        if ($event->snapshot instanceof Snapshot) {
            $event->snapshot->status = ModelStatusEnum::PUBLISHED->value;

            $event->snapshot->save();
        }
    }
}
