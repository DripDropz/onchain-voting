<?php

namespace App\Events;

use App\Models\Petition;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PetitionSigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $petition;

    /**
     * Create a new event instance.
     */
    public function __construct(Petition $petition)
    {
        $this->petition = $petition;
    }
}
