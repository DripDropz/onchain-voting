<?php

use App\DataTransferObjects\BallotData;
use App\Http\Controllers\BallotController;
use App\Http\Controllers\ProfileController;
use App\Models\Ballot;
use Illuminate\Foundation\Application;
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

Route::get('/dashboard', function () {
    $ballots = BallotData::collection(Ballot::all());
    return Inertia::render('Dashboard')->with([
        'ballots' => $ballots
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Ballot
    Route::get('/ballots/{ballot:id}', [BallotController::class, 'view'])->name('ballot.view');
    Route::get('/ballots/create', [BallotController::class, 'create'])->name('ballot.create');
    Route::get('/ballots/{ballot}/edit', [BallotController::class, 'edit'])->name('ballot.edit');

    Route::post('/ballots/create', [BallotController::class, 'store'])->name('ballot.store');
    Route::patch('/ballots/{ballot}/update', [BallotController::class, 'update'])->name('ballot.update');
    Route::delete('/ballots/{ballot}/delete', [BallotController::class, 'destroy'])->name('ballot.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


});

require __DIR__.'/auth.php';
