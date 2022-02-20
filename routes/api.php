<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/room', [App\Http\Controllers\Api\RoomController::class, 'store']);

Route::middleware(['room'])->group(function () {
    Route::get('/room', [App\Http\Controllers\Api\RoomController::class, 'show']);
    Route::delete('/room', [App\Http\Controllers\Api\RoomController::class, 'delete']);
    Route::put('/room', [App\Http\Controllers\Api\RoomController::class, 'join'])->middleware('users.number');

    Route::middleware(['users.room'])->group(function () {
        Route::get('/room/game', [App\Http\Controllers\Api\GameController::class, 'chooseNumber']);
        Route::post('/room/game', [App\Http\Controllers\Api\GameController::class, 'store']);
    });
});
