<?php

use App\Http\Controllers\{CarBrandsController, CarModelsController, UserController};
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

Route::post('/signup', [UserController::class, 'signup']);
Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('/models', CarModelsController::class)->except('show');
    Route::apiResource('/brands', CarBrandsController::class)->except('show');
    Route::get('/brands/search', [CarBrandsController::class, 'search']);
    Route::get('/user/brands', [CarBrandsController::class, 'getUserBrands']);
});



