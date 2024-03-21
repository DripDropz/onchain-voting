<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BallotController;
use App\Http\Controllers\Admin\PetitionController;
use App\Http\Controllers\Admin\SnapshotController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('/admin')->as('admin.')->middleware(['auth', 'verified', 'admin.routes'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ballot
    Route::prefix('/ballots')->as('ballots.')->group(function () {
        // Views
        Route::get('/', [BallotController::class, 'index'])->name('index');
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
            Route::get('/create', [BallotController::class, 'createPolicy'])
                ->name('create');
            Route::get('/{policy}/edit', [BallotController::class, 'editPolicy'])
                ->name('edit');

            // CRUDs
            Route::post('/create', [BallotController::class, 'storePolicy'])
                ->name('store');
            Route::patch('/{policy}/update', [BallotController::class, 'updatePolicy'])
                ->name('update');
            Route::delete('/{policy}/delete', [BallotController::class, 'destroyPolicy'])
                ->name('destroy');
            Route::post('/{policy}/image-link', [BallotController::class, 'addImageLink'])
                ->name('imageLink');
        });
    });


    // Petition
    Route::prefix('/petitions')->as('petitions.')->group(function () {
        // views
        Route::get('/', [PetitionController::class, 'index'])->name('index');

        Route::match(['get', 'patch'], '/{petition}', [PetitionController::class, 'view'])
            ->name('view');

        Route::get('/{petition:id}/edit', [PetitionController::class, 'edit'])
        ->name('edit');

        Route::get('/{petition}/ballot', [PetitionController::class, 'moveToBallot'])
        ->name('toBallot');

        Route::patch('/{petition}/update/{ballot?}', [PetitionController::class, 'update'])->name('update');

        Route::prefix('{petition}/rules')->as('rules.')->group(function () {
            Route::post('/create', [PetitionController::class, 'saveRule'])
            ->name('saveRule');
            Route::post('{rule}/delete', [PetitionController::class, 'removeRule'])
            ->name('delete');
        });

    });

     // Poll
     Route::prefix('/polls')->as('polls.')->group(function () {
        // views
        Route::get('/', [PollController::class, 'index'])->name('index');
        Route::get('/pollsData', [PollController::class, 'pollsData'])->name('pollsData');

        // Route::match(['get', 'patch'], '/{poll}', [PollController::class, 'view'])
        //     ->name('view');

        // Route::get('/{poll:id}/edit', [PollController::class, 'edit'])
        // ->name('edit');

        Route::put('/update/{poll}', [PollController::class, 'update'])
        ->name('update');
    });

    // Snapshot
    Route::prefix('/snapshots')->as('snapshots.')->group(function () {
        Route::get('/snapshot-data', [SnapshotController::class, 'snapshotsData'])->name('snapshotsData');

        // Views
        Route::get('/', [SnapshotController::class, 'index'])->name('index');
        Route::get('/create', [SnapshotController::class, 'create'])->name('create');
        Route::get('/{snapshot:id}', [SnapshotController::class, 'view'])->name('view');
        Route::get('/{snapshot:id}/edit', [SnapshotController::class, 'edit'])->name('edit');

        // CRUDs
        Route::post('/create', [SnapshotController::class, 'store'])
            ->name('store');
        Route::patch('/{snapshot}/update', [SnapshotController::class, 'update'])
            ->name('update');
        Route::delete('/{snapshot}/delete', [SnapshotController::class, 'destroy'])
            ->name('destroy');


        // Snapshot Voting Powers
        Route::prefix('/{snapshot}/powers')->as('powers.')->group(function () {
            // CRUDs
            Route::get('/', [SnapshotController::class, 'votingPowers'])
                ->name('list');

            // import voting powers from csv
            Route::prefix('/csv')->as('csv.')->group(function () {
                Route::get('/upload', [SnapshotController::class, 'uploadVotingPowerCsv'])
                    ->name('upload');
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
        $collection = 'App\\Enums\\' . Str::studly($collection) . 'Enum';

        return array_column($collection::cases(), 'value', 'name');
    })->name('enums');
});
