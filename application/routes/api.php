<?php

use App\Http\Controllers\Admin\BallotController;
use App\Http\Controllers\Admin\SnapshotController;
use App\Jobs\CreateVotingPowerSnapshotJob;
use App\Models\Snapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\LazyCollection;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/ballots')->as('config.')->group(function () {
    Route::get('/cardano', function (Request $request) {
        $credentials = [
            'blockExplorer' => env('CARDANO_BLOCK_EXPLORER'),
            'blockfrostUrl' => config('services.blockfrost.baseUrl'),
            'projectId' => config('services.blockfrost.projectId'),
        ];

        return json_encode($credentials);
    })->name('cardano');

    Route::get('/app', function (Request $request) {
        return collect(config('app'))
        ->only([
            'cardano_network',
            'hosted_by',
            'hosted_by_link',
            'logo',
            'power_by',
            'show_created_by'
        ])->toJson();
    })->name('app');
});

Route::post('/parse/csv', function (Request $request) {
    $response = Gate::inspect('update', Snapshot::class);
    LazyCollection::make(function () use ($request) {
        $filePath = $request->file('file')->getRealPath();
        $handle = fopen($filePath, 'r');

        while (($line = fgetcsv($handle, null)) !== false) {
          yield $line;
        }

        fclose($handle);
      })
      ->skip(1)
      ->chunk(10000)
      ->each(fn (LazyCollection $chunk)  =>
        $chunk->each(
            fn ($row)  => CreateVotingPowerSnapshotJob::dispatch($request->snapshot, $row[0], $row[1])
        )
      );

      return Snapshot::whereHash($request->snapshot);

    return json_encode($response);
});

Route::get('/snapshot', [SnapshotController::class, 'searchSnapshot'])->name('searchSnapshot');
Route::post('/update-position', [BallotController::class, 'updatePosition'])->name('update.position');
