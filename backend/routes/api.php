<?php

use App\Http\Controllers\Api\V1\ApproveMaterialController;
use App\Http\Controllers\Api\V1\CancelMaterialController;
use App\Http\Controllers\Api\V1\ChangePasswordController;
use App\Http\Controllers\Api\V1\CurrentTermsController;
use App\Http\Controllers\Api\V1\DeleteMaterialController;
use App\Http\Controllers\Api\V1\ListDisciplineMaterialsController;
use App\Http\Controllers\Api\V1\ListDisciplinesController;
use App\Http\Controllers\Api\V1\ListModerationMaterialsController;
use App\Http\Controllers\Api\V1\ListMyMaterialsController;
use App\Http\Controllers\Api\V1\ListNotificationsController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\ReadAllNotificationsController;
use App\Http\Controllers\Api\V1\ReadNotificationController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\RejectMaterialController;
use App\Http\Controllers\Api\V1\ShowProfileController;
use App\Http\Controllers\Api\V1\ShowStudentProfileController;
use App\Http\Controllers\Api\V1\StoreMaterialController;
use App\Http\Controllers\Api\V1\UpdateMaterialController;
use App\Http\Controllers\Api\V1\UpdateProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('terms/current', CurrentTermsController::class);
    Route::post('register', RegisterController::class)->middleware('throttle:register');
    Route::post('login', LoginController::class)->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', MeController::class);
        Route::put('me/password', ChangePasswordController::class)->middleware('throttle:password');
        Route::get('disciplines', ListDisciplinesController::class);
        Route::get('disciplines/{discipline}/materials', ListDisciplineMaterialsController::class);
        Route::post('disciplines/{discipline}/materials', StoreMaterialController::class);
        Route::get('profile', ShowProfileController::class);
        Route::get('me/materials', ListMyMaterialsController::class);
        Route::get('students/{student:public_code}', ShowStudentProfileController::class);
        Route::get('notifications', ListNotificationsController::class);
        Route::post('notifications/read-all', ReadAllNotificationsController::class);
        Route::post('notifications/{notification}/read', ReadNotificationController::class);
        Route::post('materials/{material}/cancel', CancelMaterialController::class);
        Route::put('materials/{material}', UpdateMaterialController::class);
        Route::delete('materials/{material}', DeleteMaterialController::class);
        Route::put('profile', UpdateProfileController::class);
        Route::post('logout', LogoutController::class);

        Route::middleware('can:access-moderation')->prefix('moderation')->group(function () {
            Route::get('materials', ListModerationMaterialsController::class);
            Route::post('materials/{material}/approve', ApproveMaterialController::class);
            Route::post('materials/{material}/reject', RejectMaterialController::class);
        });
    });
});
