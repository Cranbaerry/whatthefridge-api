<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateSupabaseToken;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
 

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


// Route: /api/auth/xxx
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/google', [AuthController::class, 'getGoogleAuthUrlJson']);
    Route::get('/discord', [AuthController::class, 'getDiscordAuthUrlJson']);
    Route::get('/session', [AuthController::class, 'getUserSession']);
    Route::post('/recover', [AuthController::class, 'recover']);

    Route::middleware([ValidateSupabaseToken::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Unfinished
        Route::post('/save', [RecipeController::class, 'save']);
    });
});

Route::prefix('recipes')->group(function () {
    Route::get('/favourites', [RecipeController::class, 'getFavourites']);
    Route::post('/search', [RecipeController::class, 'search']);
    Route::post('/save', [RecipeController::class, 'save']);
});
