<?php

use App\Http\Controllers\Admin\BallotController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SnapshotController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::prefix('/admin')->as('admin.')->middleware(['auth', 'verified', 'admin.routes'])->group(function () {
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
        Route::patch('/{ballot}/status/update', [BallotController::class, 'statusUpdate'])->name('status.update');

        // Ballot Snapshots
        Route::prefix('/{ballot}/snapshots')->as('snapshots.')->group(function () {
            Route::get('/link', [BallotController::class, 'viewLinkSnapshot'])->name('link.view');
            Route::post('/{snapshot}/link', [BallotController::class, 'linkSnapshot'])->name('link');
            Route::post('/{snapshot}/unlink', [BallotController::class, 'unLinkSnapShot'])->name('unLink');
        });

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
                Route::get('/edit', [BallotController::class, 'editQuestionChoice'])->name('choice.edit');

                // CRUDs
                Route::post('/create', [BallotController::class, 'storeQuestionChoice'])->name('store');
                Route::post('/edit', [BallotController::class, 'storeEditQuestionChoice'])->name('edit');
                Route::delete('/delete', [BallotController::class, 'deleteQuestionChoice'])->name('delete');
                Route::patch('/{choice}/update', [BallotController::class, 'updateQuestionChoice'])
                    ->name('update');
            });
        });

        // Ballot Policies
        Route::prefix('/{ballot}/policies')->as('policies.')->group(function () {
            // Views
            Route::get('/create', [BallotController::class, 'createPolicy'])->name('create');
            Route::get('/{policy}/edit', [BallotController::class, 'editPolicy'])->name('edit');

            // CRUDs
            Route::post('/create', [BallotController::class, 'storePolicy'])->name('store');
            Route::patch('/{policy}/update', [BallotController::class, 'updatePolicy'])
                ->name('update');
            Route::delete('/{policy}/delete', [BallotController::class, 'destroyPolicy'])->name('destroy');
            Route::post('/image-link', [BallotController::class, 'addImageLink'])->name('imageLink');
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
            Route::get('/', [SnapshotController::class, 'votingPowers'])->name('list');
            Route::post('/create', [SnapshotController::class, 'storeQuestion'])->name('store');
            Route::patch('/{question}/update', [SnapshotController::class, 'updateQuestion'])
                ->name('update');
            Route::delete('/{question}/delete', [SnapshotController::class, 'destroyQuestion'])->name('destroy');

            // import voting powers from csv
            Route::prefix('/csv')->as('csv.')->group(function () {
                Route::get('/upload', [SnapshotController::class, 'uploadVotingPowerCsv'])->name('upload');

                Route::post('/save', [SnapshotController::class, 'storeVotingPowerCsv'])->name('store');
            });
        });
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Utilities
    Route::get('/enums/{collection}', function () {
        $collection = request()->route('collection');
        $collection = 'App\\Enums\\' . Str::studly($collection). 'Enum';

        return array_column($collection::cases(), 'value', 'name');
    })->name('enums');
});
