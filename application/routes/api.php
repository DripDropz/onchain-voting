<?php

use App\Models\Petition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\BallotController;
use App\Http\Controllers\Admin\PetitionController;
use App\Http\Controllers\Admin\SnapshotController;
use App\Http\Controllers\Admin\SnapshotImportController;
use App\Http\Controllers\BlockfrostLinkController;
use App\Http\Integrations\Blockfrost\Requests\BlockfrostRequest;

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
            'network_id' => config('app.cardano_network'),
            'app_url' => config('app.url'),
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
                'powered_by',
                'show_created_by',
                'url'
            ])->toJson();
            
    })->name('app');

});

Route::prefix('/query-chain')->as('frost.')->group(
    function () {
        Route::get('/',[BlockfrostLinkController::class, 'queryChain'])->name('index');
        Route::get('/asset-detail', [BlockfrostLinkController::class, 'getAssetDetail'])->name('asset');
    }
);

Route::get('query/{params?}', function (Request $request, BlockfrostRequest $frost) {
    $uri = str_replace('api/query','', $request->getRequestUri());
    $frost->setEndPoint($uri);
    $response = $frost->send();

    return $response->json();
})->where('params', '.*')->name('blockfrost-query');


Route::post('/parse/csv', [SnapshotImportController::class, 'parseCSV']);
Route::get('/parsed/csv/{filename}', [SnapshotImportController::class, 'getParsedCSV']);
Route::post('/parse/csv/cancel', [SnapshotImportController::class, 'cancelParsedCSV']);
Route::post('/upload/csv/{snapshot}', [SnapshotImportController::class, 'uploadCsv']);

Route::get('/snapshot', [SnapshotController::class, 'searchSnapshot'])->name('searchSnapshot');
Route::post('/update-position', [BallotController::class, 'updatePosition'])->name('update.position');

Route::get('/ballots', [BallotController::class, 'ballotsData'])->name('ballotsData');
Route::get('/all-ballots', [BallotController::class, 'allBallots'])->name('allBallots');
Route::get('/petitions', [PetitionController::class, 'petitionsData'])->name('petitionsData');


