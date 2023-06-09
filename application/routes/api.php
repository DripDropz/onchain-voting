<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SnapshotController;

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

Route::get('/cardano/config', function (Request $request) {
    $credentials = [
        'poolId' => env('CARDANO_POOL_HASH'),
        'blockExplorer' => env('CARDANO_BLOCK_EXPLORER'),
        'blockfrostUrl' => config('services.blockfrost.baseUrl'),
        'projectId' => config('services.blockfrost.projectId'),
    ];

    return json_encode($credentials);
});

Route::post('/parse/csv', function (Request $request) {
    $response = Gate::inspect('update', Snapshot::class);
       
    $filePath = $request->file('file')->getRealPath();
    $csv = file_get_contents($filePath);
    $content = array_map("str_getcsv", explode("\n", $csv));
    $headers = ['voter_id', 'voting_power'];
    $json = [];
    foreach ($content as $row_index => $row_data) {
        foreach ($row_data as $col_idx => $col_val) {
            $label = $headers[$col_idx];
            switch ($label) {
                case 'voter_id':
                    if (strlen($col_val) > 15 && substr($col_val, 0, 5) == 'stake') {
                        $json[$row_index][$label] = $col_val;
                    }
                case 'voting_power':
                    if (is_numeric($col_val)) {
                        $json[$row_index][$label] = $col_val;
                    }
            }
        }
    }
    
    $partialData = count($json) >= 10 ? array_slice($json, 0, 10) : $json;
    $response = [
        'partialData' => $partialData,
        'data' => $json,
    ];
    return json_encode($response);
});

Route::get('/snapshot', [SnapshotController::class,'searchSnapshot'])->name('searchSnapshot');
