<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\BallotData;
use App\DataTransferObjects\PetitionData;
use App\DataTransferObjects\PollData;
use App\Http\Controllers\Controller;
use App\Models\Ballot;
use App\Models\Petition;
use App\Models\Poll;
use App\Models\Snapshot;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {

        $snapshots = Snapshot::latest()->limit(3)->get();
        $ballots = BallotData::collection(Ballot::latest()->limit(3)->get());

        // Show only petitions awaiting admin approval (pending + approved)
        $petitions = PetitionData::collection(
            Petition::whereIn('status', ['pending', 'approved'])
                ->latest()
                ->with('ballot')
                ->limit(10)
                ->get()
        );

        // Show only polls awaiting admin approval (pending + approved)
        $polls = PollData::collection(
            Poll::whereIn('status', ['pending', 'approved'])
                ->latest()
                ->limit(10)
                ->get()
        );

        // Get counts for dashboard display
        $petitionCounts = [
            'awaitingReview' => Petition::whereIn('status', ['pending', 'approved'])->count(),
            'total' => Petition::count(),
        ];

        $pollCounts = [
            'awaitingReview' => Poll::whereIn('status', ['pending', 'approved'])->count(),
            'total' => Poll::count(),
        ];

        return Inertia::render('Auth/Dashboard')->with([
            'ballots' => $ballots,
            'snapshots' => $snapshots,
            'petitions' => $petitions,
            'polls' => $polls,
            'petitionCounts' => $petitionCounts,
            'pollCounts' => $pollCounts,
        ]);
    }
}
