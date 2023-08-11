<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotData;
use App\Models\Ballot;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BallotController extends Controller
{
    /**
     * Display the ballot's form.
     */
    public function view(Request $request, Ballot $ballot): Response
    {
        $ballot->load(['questions.choices', 'user_response.choice']);
        // dd($ballot->toArray());
        return Inertia::render('Ballot/View', [
            'ballot' => BallotData::from($ballot),
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function registerView(Request $request, Ballot $ballot)
    {

        $ballot->load(['questions.choices', 'user_response.choice']);

        return Inertia::modal('Ballot/Register')
            ->with([
                'ballot' => BallotData::from($ballot),
            ])
            ->baseRoute('ballot.view', $ballot->hash);
    }

    /**
     * Display the ballot's form.
     */
    public function registerStore(Request $request, Ballot $ballot): Response
    {
        $ballot->load(['questions.choices', 'user_response.choice']);

        return Inertia::render('Ballot/Register', [
            'ballot' => BallotData::from($ballot),
        ]);
    }
}
