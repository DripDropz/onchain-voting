<?php

use App\Http\Controllers\Admin\BallotController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SnapshotController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::prefix('/admin')->as('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ballot
    Route::prefix('/ballots')->as('ballots.')->group(function () {
        Route::get('/', [BallotController::class, 'index'])->name('index');

        // Views
        Route::get('/create', [BallotController::class, 'create'])->name('create');
        Route::get('/{ballot:id}', [BallotController::class, 'view'])->name('view');
        Route::get('/{ballot}/edit', [BallotController::class, 'edit'])->name('edit');

        // CRUDs
        Route::post('/create', [BallotController::class, 'store'])->name('store');
        Route::patch('/{ballot}/update', [BallotController::class, 'update'])->name('update');
        Route::delete('/{ballot}/delete', [BallotController::class, 'destroy'])->name('destroy');

        // Ballot Questions
        Route::prefix('/{ballot}/questions')->as('questions.')->group(function () {
            // Views
            Route::get('/create', [BallotController::class, 'createQuestion'])->name('create');
            Route::get('/{question}/edit', [BallotController::class, 'editQuestion'])->name('edit');

            // CRUDs
            Route::post('/create', [BallotController::class, 'storeQuestion'])->name('store');
            Route::patch('/{question}/update', [BallotController::class, 'updateQuestion'])
                ->name('update');
            Route::delete('/{question}/delete', [BallotController::class, 'destroyQuestion'])->name('destroy');

            // Ballot Questions Choices
            Route::prefix('/{question:id}/choices')->as('choices.')->group(function () {
                // Views
                Route::get('/create', [BallotController::class, 'createQuestionChoice'])->name('create');

                // CRUDs
                Route::post('/create', [BallotController::class, 'storeQuestionChoice'])->name('store');
                Route::patch('/{choice}/update', [BallotController::class, 'updateQuestionChoice'])
                    ->name('update');
            });
        });
    });

    // Snapshot
    Route::prefix('/snapshots')->as('snapshots.')->group(function () {
        // Views
        Route::get('/', [SnapshotController::class, 'index'])->name('index');
        Route::get('/create', [SnapshotController::class, 'create'])->name('create');
        Route::get('/{snapshot:id}', [SnapshotController::class, 'view'])->name('view');
        Route::get('/{snapshot:id}/edit', [SnapshotController::class, 'edit'])->name('edit');

        // CRUDs
        Route::post('/create', [SnapshotController::class, 'store'])->name('store');
        Route::patch('/{snapshot}/update', [SnapshotController::class, 'update'])->name('update');
        Route::delete('/{snapshot}/delete', [SnapshotController::class, 'destroy'])->name('destroy');

        // Snapshot Voting Powers
        Route::prefix('/{snapshot}/powers')->as('powers.')->group(function () {
            // Views
            Route::get('/create', [SnapshotController::class, 'createQuestion'])->name('create');
            Route::get('/{question}/edit', [SnapshotController::class, 'editQuestion'])->name('edit');

            // CRUDs
            Route::post('/create', [SnapshotController::class, 'storeQuestion'])->name('store');
            Route::patch('/{question}/update', [SnapshotController::class, 'updateQuestion'])
                ->name('update');
            Route::delete('/{question}/delete', [SnapshotController::class, 'destroyQuestion'])->name('destroy');
        });
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Utilities
    Route::get('/enums/{collection}', function () {
        $collection = request()->route('collection');
        $collection = 'App\\Enums\\' . Str::studly($collection) . 'Enum';

        return array_column($collection::cases(), 'value', 'name');
    })->name('enums');
});
