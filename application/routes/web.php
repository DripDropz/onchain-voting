<?php

use App\Http\Controllers\BallotController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetitionController;
use App\Http\Controllers\PollController;

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
    Route::get('/', [BallotController::class, 'view'])
        ->name('view');

    Route::get('/register', [BallotController::class, 'viewRegistration'])
        ->name('register.view')
        ->middleware(['snapshot.check', 'auth.voter']);

    Route::post('/registration/start', [BallotController::class, 'startRegistration'])
        ->name('register.store');
    Route::post('/registration/submit', [BallotController::class, 'submitRegistration'])
        ->name('register.submit');
    Route::post('/registration/update', [BallotController::class, 'saveUpdateRegistration'])
        ->name('register.save-update');

    Route::get('/missing-snapshot', [BallotController::class, 'missingSnapshot'])->name('missing.snapshot');
    Route::get('/missing-snapshot', [BallotController::class, 'missingSnapshot'])->name('missing.snapshot');
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
        Route::post('/save', [VoterController::class, 'saveBallotResponse'])
            ->name('save');
    });
});

// Petition
Route::prefix('/petitions')->as('petitions.')->group(function () {
    Route::get('/', [PetitionController::class, 'index'])
        ->name('index');

    Route::prefix('/workflow')->group(function() {
        Route::get('/create/{petition?}', [PetitionController::class, 'create'])->name('create');
        Route::get('/create/{petition}/step/1', [PetitionController::class, 'create'])->name('create.stepOne');

        Route::get('/create/{petition}/step/2', [PetitionController::class, 'stepTwo'])->name('create.stepTwo');

        Route::get('/create/{petition}/step/3', [PetitionController::class, 'stepThree'])->name('create.stepThree');
    });

    Route::patch('/{petition}/update', [PetitionController::class, 'update'])->name('update');

    Route::patch('/{petition}/close', [PetitionController::class, 'close'])->name('close');

    Route::post('/store', [PetitionController::class, 'store'])->name('store');

    Route::get('/{petition}/manage', [PetitionController::class, 'manage'])
        ->middleware(['auth', 'verified'])
        ->name('manage');

    Route::get('/{petition}/edit', [PetitionController::class, 'edit'])->name('edit');

    // rules
    Route::prefix('{petition}/rules')->as('rules.')->group(function () {
        Route::get('/create', [PetitionController::class, 'makeRule'])
            ->name('create');
        Route::post('/create', [PetitionController::class, 'saveRule'])
            ->name('saveRule');
        Route::get('{rule}/delete', [PetitionController::class, 'deleteRule'])
            ->name('removeRule');
        Route::post('{rule}/delete', [PetitionController::class, 'removeRule'])
            ->name('delete');
    });

    Route::prefix('{petition}/signatures')->as('signatures.')->group(function () {
        Route::post('/sign', [PetitionController::class, 'signPetition'])
            ->name('store');
    });

    Route::get('/{petition}', [PetitionController::class, 'view'])
    ->name('view');

    Route::get('/{petition}/share', [PetitionController::class, 'share'])
    ->name('share');
});

//Polls
Route::prefix('/polls')->as('polls.')->group(function () {
    Route::get('/', [PollController::class, 'index'])
        ->name('index');
    Route::get('/create', [PollController::class, 'create'])
    ->name('create');
    Route::post('/create', [PollController::class, 'store'])
    ->name('store');
});


require __DIR__ . '/admin.php';

require __DIR__ . '/auth.php';
