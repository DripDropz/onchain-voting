<?php

use App\Http\Controllers\BallotController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'view'])->name('home');

// Ballot
Route::prefix('/ballots')->as('ballots.')->group(function () {

});

Route::prefix('/ballots/{ballot}')->as('ballot.')->group(function () {
    Route::get('/', [BallotController::class, 'view'])->name('view');

    Route::get('/register', [BallotController::class, 'registerView'])->name('register.view');
    Route::post('/register', [BallotController::class, 'registerStore'])->name('register.store');
});

// Voter
Route::prefix('/voters')->as('voters.')->group(function () {
    Route::get('/{voterId}/power/{ballot:id}', [VoterController::class, 'power'])->name('power');
    Route::get('/{voterId}', [VoterController::class, 'voter'])->name('voter');

    // Voter Ballot Reponses
    Route::prefix('/{voterId}/ballot-responses')->as('ballot-responses.')->group(function () {
        Route::post('/save', [VoterController::class, 'saveBallotResponse'])->name('save');
    });
    Route::post('/{voterId}/submit-vote', [VoterController::class, 'submitVote'])->name('submitVote');

});

require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
