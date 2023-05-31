<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

