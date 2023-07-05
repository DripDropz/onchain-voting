<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\BallotData;
use App\DataTransferObjects\SnapshotData;
use App\Http\Controllers\Controller;
use App\Models\Ballot;
use App\Models\Snapshot;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $ballots = BallotData::collection(Ballot::all());
        $snapshots = SnapshotData::collection(Snapshot::all());

        return Inertia::render('Auth/Dashboard')->with([
            'ballots' => $ballots,
            'snapshots' => $snapshots,
        ]);
    }
}
