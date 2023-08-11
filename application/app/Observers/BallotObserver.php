<?php

namespace App\Observers;

class BallotObserver
{
    /**
     * Handle events after all transactions are committed.
     */
    public bool $afterCommit = true;

    /**
     * Handle the User "created" event.
     */
    public function creating(User $user): void
    {
        // ...
    }

    /**
     * Handle the User "updated" event.
     */
    public function updating(User $user): void
    {
        // ...
    }
}
