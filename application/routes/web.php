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
    Route::get('/', [BallotController::class, 'index'])->name('index');
});

Route::prefix('/ballots/{ballot}')->as('ballot.')->group(function () {
    Route::get('/', [BallotController::class, 'view'])->name('view');

    Route::get('/register', [BallotController::class, 'viewRegistration'])->name('register.view')
        ->middleware(['snapshot.check', 'auth.voter']);

    Route::post('/registration/start', [BallotController::class, 'startRegistration'])
        ->name('register.store');
    Route::post('/registration/submit', [BallotController::class, 'submitRegistration'])
        ->name('register.submit');

    Route::get('/missing-snapshot', [BallotController::class, 'missingSnapshot'])->name('missing.snapshot');    Route::get('/missing-snapshot', [BallotController::class, 'missingSnapshot'])->name('missing.snapshot');
    Route::get('/policy-id/{policyType}', [BallotController::class, 'policyId'])->name('policyId');

    Route::post('/vote/start', [BallotController::class, 'startVoting'])->name('startVoting');
    Route::post('/vote/submit', [BallotController::class, 'completeVoting'])->name('completeVoting');
});


// Voter
Route::prefix('/voters')->as('voters.')->group(function () {
    Route::get('/{voterId}/power/{ballot:id}', [VoterController::class, 'power'])->name('power');
    Route::get('/{voterId}', [VoterController::class, 'voter'])->name('voter');

    // Voter Ballot Reponses
    Route::prefix('/{voterId}/ballot-responses')->as('ballot-responses.')->group(function () {
        Route::post('/save', [VoterController::class, 'saveBallotResponse'])->name('save');
    });
});

require __DIR__.'/admin.php';

require __DIR__.'/auth.php';
