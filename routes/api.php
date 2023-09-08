<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PokemonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('pokemon')->group(function () {
    Route::get('', [PokemonController::class, 'index']);
    // Favorite Route Api
    Route::prefix('favorite')->group(function () {
        Route::get('', [FavoriteController::class, 'index']);
        Route::post('', [FavoriteController::class, 'store']);
        Route::delete('{id}', [FavoriteController::class, 'destroy']);
    });
    // Ability Route Api
    Route::prefix('ability')->group(function () {
        Route::get('', [AbilityController::class, 'index']);
    });
    Route::get('{id}', [PokemonController::class, 'show']);
});
