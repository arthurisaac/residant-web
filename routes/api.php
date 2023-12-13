<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyPictureController;
use App\Http\Controllers\ReservationController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('me', [AuthController::class, 'detail']);
    Route::resource('reservations', ReservationController::class);
    Route::resource('favorites', FavoriteController::class);

    Route::post('change-password', [AuthController::class, 'change_password']);
    Route::post('/me/update', [AuthController::class, 'change_profile']);

    Route::post('property-update/{id}', [PropertyController::class, 'updateProperty']);
    Route::get('my-properties', [PropertyController::class, 'myProperties']);

    Route::get('my-reservations', [ReservationController::class, 'myReservations']);
});

Route::resource("properties", PropertyController::class);
Route::resource("properties-pictures", PropertyPictureController::class);
Route::get('reservations-indisponibles', [ReservationController::class, 'availableData']);
Route::get('search-properties', [PropertyController::class, 'search']);


