<?php

use App\DataTransferObjects\BallotData;
use App\Http\Controllers\Admin\BallotController;
use App\Http\Controllers\ProfileController;
use App\Models\Ballot;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::middleware(['auth', 'verified'])
Route::prefix('/admin')->as('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $ballots = BallotData::collection(Ballot::all());
        return Inertia::render('Auth/Dashboard')->with([
            'ballots' => $ballots
        ]);
    })->name('dashboard');

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
        Route::prefix('/{ballot}/questions')->as('questions.')->group(function () {
            Route::get('/create', [BallotController::class, 'createQuestion'])->name('create');

            Route::post('/create', [BallotController::class, 'storeQuestion'])->name('store');
            Route::patch('/{question}/update', [BallotController::class, 'updateQuestion'])
                ->name('update');

            // Ballot Questions Choices
            Route::prefix('/{question:id}/choices')->as('choices.')->group(function () {
                Route::get('/create', [BallotController::class, 'createQuestionChoice'])->name('create');

                Route::post('/create', [BallotController::class, 'storeQuestionChoice'])->name('store');
                Route::patch('/{choice}/update', [BallotController::class, 'updateQuestionChoice'])
                    ->name('update');
            });
        });
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
})->middleware(['auth', 'verified']);
