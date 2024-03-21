<?php

namespace App\Listeners;

use App\Jobs\UpdatePetitionVisibilityAndFeatured;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdatePetitionAfterSignature
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $petition = $event->petition;
        UpdatePetitionVisibilityAndFeatured::dispatch($petition);
    }
}
