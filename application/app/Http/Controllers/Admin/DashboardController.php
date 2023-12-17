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
    public  $perPage = 6;

    public $currPage = 1;

    public $perPageRef = 6;

    public $currPageRef = 1;

    public function index(Request $request): Response
    {
        $this->perPage = $request->input('l',6);
        $this->currPage = $request->input('p',1);
        $this->perPageRef = $request->input('ep',6);
        $this->currPageRef = $request->input('tp',1);


        $ballots = $this->query();
        $snapshots = SnapshotData::collection(Snapshot::query()
                   ->paginate($this->perPageRef,['*'],'p', $this->currPageRef)
                   ->setPath('/')
                   ->onEachSide(1));

        return Inertia::render('Auth/Dashboard')->with([
            'ballots' => $ballots,
            'snapshots' => $snapshots,
        ]);
    }

    public function query()
    {
        return BallotData::collection(Ballot::query()
            ->paginate($this->perPage, ['*'], 'p', $this->currPage)
            ->setPath('/')
            ->onEachSide(1));
    }
}
