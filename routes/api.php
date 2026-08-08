<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Public routes
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/auth/login',
        [AuthController::class, 'login']
    )->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | Protected routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::apiResource(
            'teachers',
            TeacherController::class
        );
    });

    Route::middleware('auth:sanctum')
        ->group(function (): void {
            Route::get(
                '/auth/me',
                [AuthController::class, 'me']
            );

            Route::post(
                '/auth/logout',
                [AuthController::class, 'logout']
            );

            Route::post(
                '/auth/logout-all',
                [AuthController::class, 'logoutAll']
            );

            Route::apiResource(
                'users',
                UserController::class
            );
        });


});
