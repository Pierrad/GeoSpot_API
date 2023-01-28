<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiscoveredPlaceController;
use App\Http\Controllers\PlaceController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::prefix('place')->group(function () {
        Route::post('/', [PlaceController::class, 'create']);
        Route::put('/{place}', [PlaceController::class, 'update']);
        Route::delete('/{place}', [PlaceController::class, 'delete']);
        Route::get('/{place}', [PlaceController::class, 'get']);
        Route::post('/around', [PlaceController::class, 'getAround']);
        Route::post('/discovered', [DiscoveredPlaceController::class, 'create']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/created', [PlaceController::class, 'getCreated']);
        Route::get('/discovered', [DiscoveredPlaceController::class, 'getDiscovered']);
    });
});
