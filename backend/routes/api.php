<?php

use App\Http\Controllers\Api\V1\CurrentTermsController;
use App\Http\Controllers\Api\V1\ListDisciplineMaterialsController;
use App\Http\Controllers\Api\V1\ListDisciplinesController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\ShowProfileController;
use App\Http\Controllers\Api\V1\StoreMaterialController;
use App\Http\Controllers\Api\V1\UpdateProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('terms/current', CurrentTermsController::class);
    Route::post('register', RegisterController::class)->middleware('throttle:register');
    Route::post('login', LoginController::class)->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', MeController::class);
        Route::get('disciplines', ListDisciplinesController::class);
        Route::get('disciplines/{discipline}/materials', ListDisciplineMaterialsController::class);
        Route::post('disciplines/{discipline}/materials', StoreMaterialController::class)->middleware('throttle:materials');
        Route::get('profile', ShowProfileController::class);
        Route::put('profile', UpdateProfileController::class);
        Route::post('logout', LogoutController::class);
    });
});
