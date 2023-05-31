<?php

use App\DataTransferObjects\BallotData;
use App\Http\Controllers\BallotController;
<<<<<<< Updated upstream
use App\Http\Controllers\ProfileController;
use App\Models\Ballot;
use Illuminate\Foundation\Application;
=======
use App\Http\Controllers\VoterController;
use App\Http\Controllers\HomeController;
>>>>>>> Stashed changes
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

<<<<<<< Updated upstream
Route::get('/dashboard', function () {
    $ballots = BallotData::collection(Ballot::all());
    return Inertia::render('Dashboard')->with([
        'ballots' => $ballots
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Ballot
    Route::prefix('/ballots')->as('ballots.')->group(function () {
        Route::get('/', [BallotController::class, 'index'])->name('index');

        Route::get('/create', [BallotController::class, 'create'])->name('create');
        Route::get('/{ballot:id}', [BallotController::class, 'view'])->name('view');
        Route::get('/{ballot}/edit', [BallotController::class, 'edit'])->name('edit');

        Route::post('/create', [BallotController::class, 'store'])->name('store');
        Route::patch('/{ballot}/update', [BallotController::class, 'update'])->name('update');
        Route::delete('/{ballot}/delete', [BallotController::class, 'destroy'])->name('destroy');

        // Ballot Questions
        Route::prefix('/ballots/{ballot}/questions')->as('questions.')->group(function () {
            Route::get('/create', [BallotController::class, 'createQuestion'])->name('create');

            Route::post('/create', [BallotController::class, 'storeQuestion'])->name('store');
            Route::patch('/{question}/update', [BallotController::class, 'updateQuestion'])
                ->name('update');
        });
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
=======
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
>>>>>>> Stashed changes

require __DIR__ . '/auth.php';
