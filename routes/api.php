<?php

use App\Http\Controllers\API\API_AssetController;
use App\Http\Controllers\API\API_StockopnameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('stockopname/{code}', [API_StockopnameController::class, 'getStockopname']);
Route::post('stockopname/{code}', [API_StockopnameController::class, 'postStockopname']);
Route::post('nonaktifStockopname/{code}', [API_StockopnameController::class, 'nonaktifStockopname']);

Route::get('getAsset/{code}', [API_AssetController::class, 'getAsset']);
