<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\BallotData;
use App\DataTransferObjects\PollData;
use App\DataTransferObjects\SnapshotData;
use App\DataTransferObjects\PetitionData;
use App\Http\Controllers\Controller;
use App\Models\Ballot;
use App\Models\Petition;
use App\Models\Snapshot;
use App\Models\Poll;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{

    public function index(Request $request): Response
    {

        $snapshots = Snapshot::latest()->limit(3)->get();
        $ballots = BallotData::collection(Ballot::latest()->limit(3)->get());
        $petitions = PetitionData::collection(Petition::latest()->with('ballot')->limit(2)->get());
        $polls = PollData::collection(Poll::latest()->limit(2)->get());



        return Inertia::render('Auth/Dashboard')->with([
            'ballots' => $ballots,
            'snapshots' => $snapshots,
            'petitions' => $petitions,
            'polls' => $polls,
        ]);
    }
}
