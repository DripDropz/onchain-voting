<?php

namespace App\Observers;

use App\Models\Ballot;

class BallotPolicyObserver
{
    public function saving(Ballot $ballot)
    {
        $this->validatePolicies($ballot);
    }

    /**
     * Handle the Ballot "updating" event.
     *
     * @param  \App\Models\Ballot  $ballot
     * @return void
     */
    public function updating(Ballot $ballot)
    {
        $this->validatePolicies($ballot);
    }

    /**
     * Validate the number of attached policies for a ballot.
     *
     * @param  \App\Models\Ballot  $ballot
     * @return void
     */
    private function validatePolicies(Ballot $ballot)
    {
        $numberOfPolicies = $ballot->policies()->count();

        if ($numberOfPolicies > 2) {
            throw new \Exception('A ballot cannot have more than two policies attached.');
        }
    }
}
