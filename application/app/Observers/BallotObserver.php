<?php

namespace App\Observers;

use App\Models\Ballot;

class BallotObserver
{
    /**
     * Handle events after all transactions are committed.
     */
    public bool $afterCommit = true;

    /**
     * Handle the User "created" event.
     */
    public function creating(Ballot $ballot)
    {
        $policyCount = $ballot->policies()->count();

        if ($policyCount > 2) {
            throw new \Exception('You cannot add more than 2 policies to a ballot.');
        }
    }

    public function updating(Ballot $ballot)
    {
        $policyCount = $ballot->policies()->count();

        if ($policyCount && $ballot->policies()->newPivotRecords()->count() > 2) {
            throw new \Exception('You cannot add more than 2 policies to a ballot.');
        }
    }
}
