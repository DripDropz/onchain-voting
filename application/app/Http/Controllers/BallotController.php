<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotData;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Ballot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
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
        try{
            $request->validate([
                'title' => 'required',
                'description' => 'nullable',
                'version' => 'required',
                'status' => 'required',
                'type' => 'required',
            ]);

            $user = Auth::user();

            if ($user->hasRole('super-admin') == true) {
                $ballot = new Ballot();
                $ballot->title = $request->title;
                $ballot->description = $request->description;
                $ballot->version = $request->version;
                $ballot->status = $request->status;
                $ballot->type = $request->type;
                $ballot->save();
                
                return Redirect::route('ballots.view', ['ballot' => $ballot->hash]);
            }

            return redirect()->back();
        }catch (\Exception $e) {
            throw new \Exception("Fill all input fields.");
        }

    }


    /**
     * Update the ballot's profile information.
     */
    public function update(Request $request, $ballot): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
        ]);

        $user = Auth::user();

        $existingBallot = Ballot::byHash($ballot);
        $startedDateTime = $existingBallot->started_at ?? null;
        $currentDateTime = Carbon::now($tz='UTC');
        
        if(($startedDateTime == null || $startedDateTime > $currentDateTime) && $user->hasRole('super-admin')) {
            $existingBallot->insert([
                'title' => $request->title,
                'description' => $request->description,
                'version' => $request->version,
            ]);

            return Redirect::route('ballots.view', ['ballot' => $existingBallot->hash]);
        }

        return Redirect::route('ballots.edit', ['ballot' => $ballot]);
    }

    /**
     * Delete the ballot's account.
     */
    public function destroy(Request $request, $ballot): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super-admin') == true) {
            $existingBallot = Ballot::byHash($ballot);
            $existingBallot->delete();

            return Redirect::to('/');
        }

        return Redirect::route('ballots.view', ['ballot' => $ballot]);
    }
}
