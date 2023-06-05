<?php

use App\Http\Controllers\BallotController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\HomeController;
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
    Route::get('/{ballot}', [BallotController::class, 'view'])->name('view');
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


require __DIR__ . '/admin.php';

require __DIR__ . '/auth.php';
