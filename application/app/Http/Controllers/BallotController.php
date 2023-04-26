<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotData;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Ballot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class BallotController extends Controller
{
    /**
     * Display the new ballot's form.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Ballot/Create', [
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function view(Request $request, Ballot $ballot): Response
    {
        return Inertia::render('Ballot/View', [
            'ballot' => BallotData::from($ballot)
        ]);
    }


    /**
     * Display the ballot's form.
     */
    public function edit(Request $request, Ballot $ballot): Response
    {
        return Inertia::render('Ballot/Edit', [
            'ballot' => BallotData::from($ballot)
        ]);
    }


    /**
     * Store a newly created Ballot in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $ballot = new Ballot();
        $ballot->title = $request->title;
        $ballot->description = $request->description;
        $ballot->version = $request->version;
        $ballot->status = $request->status;
        $ballot->type = $request->type;
        $ballot->save();

        return Redirect::route('ballot.view', ['ballot' => $ballot->hash]);
    }


    /**
     * Update the ballot's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        return Redirect::route('ballot.edit');
    }

    /**
     * Delete the ballot's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        return Redirect::to('/');
    }
}
