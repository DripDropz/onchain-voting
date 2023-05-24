<?php

namespace App\Observers;

use App\Models\Snapshot;
use Illuminate\Support\Facades\Auth;

class SnapshotObserver
{
    public function creating(Snapshot $snapshot): void
    {
        $snapshot->user_id = Auth::id();
    }
}
