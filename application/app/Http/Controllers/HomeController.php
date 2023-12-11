<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotData;
use App\Models\Ballot;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function view()
    {

        $ballots = BallotData::collection(
            Ballot::with([
                'questions.choices',
                'user_responses.choices',
            ])->orderBy('started_at')->limit(4)->published()->get()
        );

        return Inertia::render('Home', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'ballots' => $ballots,
        ]);
    }
}
