<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerRestaurantController;
use App\Http\Controllers\ManagerRestaurantController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
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

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/manager', [AuthController::class, 'registerRestaurantManager']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum', 'tenant'])->prefix('manager')->group(function () {
    Route::put('/assign-category', [ManagerRestaurantController::class, 'assignCategories']);
    Route::post('/add-menu', [ManagerRestaurantController::class, 'addMenu']);
    Route::post('/remove-menu', [ManagerRestaurantController::class, 'removeMenu']);
});

Route::prefix('customer')->group(function () {
    Route::get('/restaurant', [CustomerRestaurantController::class, 'index']);
    Route::get('/restaurant/{restaurant}', [CustomerRestaurantController::class, 'show']);
    Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
        Route::post('/add-item/{restaurant}', [CustomerRestaurantController::class, 'addToCart']);
        Route::post('/checked-out-cart/{restaurant}', [CustomerRestaurantController::class, 'checkedOutCart']);
    });
});


Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('my-account', [UserController::class, 'myAccount']);
});

Route::get('/category', CategoryController::class);
