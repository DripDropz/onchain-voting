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
        $ballot->load(['questions.choices']);
        return Inertia::render('Ballot/View', [
            'ballot' => BallotData::from($ballot)
        ]);
    }
}
