<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\SnapshotData;
use App\DataTransferObjects\VotingPowerData;
use App\Http\Controllers\Controller;
use App\Jobs\CreateVotingPowerSnapshotJob;
use App\Models\Snapshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class SnapshotController extends Controller
{
    /**
     * Display the new snapshot's form.
     */
    public function index(Request $request): Response
    {
        $crumbs = [
            ['label' => 'Snapshots', 'link' => route('admin.snapshots.index')],
            ['label' => 'Create Snapshot', 'link' => route('admin.snapshots.create')],
        ];

        return Inertia::render('Auth/Snapshot/Index')->with([
            'crumbs' => $crumbs,
        ]);
    }


    public function snapshotsData(Request $request)
    {
        $page = $request->query('page') ?? 1;
        $perPage = $request->query('perPage') ?? 6;

        $snapshots = Snapshot::paginate($perPage, ['*'], 'p', $page)->setPath('/')->onEachSide(0);

        return SnapshotData::collection($snapshots);
    }

    /**
     * Display the new snapshot's form.
     */
    public function create(Request $request): Response
    {
        $crumbs = [
            ['label' => 'Snapshots', 'link' => route('admin.snapshots.index')],
            ['label' => 'Create Snapshot', 'link' => route('admin.snapshots.create')],
        ];

        return Inertia::render('Auth/Snapshot/Create')->with([
            'crumbs' => $crumbs,
        ]);
    }


    /**
     * Display the snapshot's form.
     */
    public function view(Request $request, Snapshot $snapshot): Response
    {
        $crumbs = [
            ['label' => 'Snapshots', 'link' => route('admin.snapshots.index')],
            ['label' => 'View Snapshot', 'link' => route('admin.snapshots.view', ['snapshot' => $snapshot->hash])],
        ];
        return Inertia::render('Auth/Snapshot/View', [
            'snapshot' => SnapshotData::from($snapshot),
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display the snapshot's form.
     */
    public function edit(Request $request, Snapshot $snapshot): Response
    {
        $snapshot->load(['ballot']);
        $crumbs = [
            ['label' => 'Snapshots', 'link' => route('admin.snapshots.index')],
            ['label' => 'Edit Snapshot', 'link' => route('admin.snapshots.edit', ['snapshot' => $snapshot->hash])],
        ];
        return Inertia::render('Auth/Snapshot/Edit', [
            'snapshot' => SnapshotData::from($snapshot),
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Store a newly created Snapshot in storage.
     */
    public function store(SnapshotData $snapshotData): RedirectResponse
    {
        $response = Gate::inspect('create', [Snapshot::class]);

        if ($response->allowed()) {
            $snapshot = new Snapshot;
            $snapshot->fill($snapshotData->all());
            $snapshot->save();

            return Redirect::route('admin.snapshots.view', ['snapshot' => $snapshot->hash]);
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to create snapshot']);
        }
    }

    /**
     * Update the snapshot's profile information.
     */
    public function update(Request $request, Snapshot $snapshot)
    {
        $response = Gate::inspect('update', $snapshot);

        if ($response->allowed()) {
            $snapshot = Snapshot::byHash($snapshot->hash);
            $snapshot->title = $request->title;
            $snapshot->description = $request->description;
            $snapshot->status = $request->status;
            $snapshot->type = $request->type;
            $snapshot->policy_id = $request->policy_id;
            $snapshot->update();

            return Redirect::back();
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized']);
        }
    }

    /**
     * Delete the snapshot's account.
     */
    public function destroy(Request $request, Snapshot $snapshot): RedirectResponse
    {
        $response = Gate::inspect('delete', $snapshot);

        if ($response->allowed()) {
            $snapshot->load('voting_powers');
            $snapshot->delete();

            return to_route('admin.dashboard');
        } else {
            return Redirect::route('admin.snapshots.view', ['snapshot' => $snapshot]);
        }
    }

    public function votingPowers(Request $request, Snapshot $snapshot)
    {
        $response = Gate::inspect('view', [Snapshot::class]);
        $page = $request->query('page') ?? 1;
        $perPage = $request->query('perPage') ?? 40;
        $sort = $request->query('sort');

        if ($response->allowed()) {
            if (!is_null($sort)) {
                [$sortColumn, $sortOrder] = explode(':', $sort);
                $votingPowers = $snapshot->voting_powers()->orderBy($sortColumn, $sortOrder);
            } else {
                $votingPowers = $snapshot->voting_powers();
            }

            return VotingPowerData::collection($votingPowers->with(['user'])->paginate($perPage)->onEachSide($page));
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to view voting power']);
        }
    }

    public function uploadVotingPowerCsv(Request $request, Snapshot $snapshot)
    {
        $response = Gate::inspect('update', Snapshot::class);

        if ($response->allowed()) {
            return Inertia::modal('Auth/Snapshot/Partials/VotingPowerImporterModal')
                ->with([
                    'snapshot' => SnapshotData::from($snapshot),
                ])
                ->baseRoute('admin.dashboard');
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to import voting power']);
        }
    }

    public function storeVotingPowerCsv(Request $request, Snapshot $snapshot)
    {
        $response = Gate::inspect('update', Snapshot::class);

        if ($response->allowed()) {
            $uploadedFile = $request->parsedCsv;

            foreach ($uploadedFile as $voter) {
                CreateVotingPowerSnapshotJob::dispatch($snapshot->hash, $voter['voter_id'], $voter['voting_power']);
            }
            return redirect()
                ->route('admin.snapshots.view', ['snapshot' => $snapshot->hash]);
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to import voting power']);
        }
    }

    public function searchSnapshot(Request $request)
    {
        $term = $request->input('term');

        return Snapshot::where('title', 'iLIKE', "%{$term}%")
            ->get()
            ->take(5)
            ->map(fn ($q) => [
                'title' => $q->title,
                'hash' => $q->hash,
            ]);
    }
}
