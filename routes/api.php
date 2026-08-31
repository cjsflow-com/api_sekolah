<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EducationUnitController;
use App\Http\Controllers\Api\V1\FeeTypeController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SchoolClassController;
use App\Http\Controllers\Api\V1\SemesterController;
use App\Http\Controllers\Api\V1\StudentAuthController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\TeacherAuthController;
use App\Http\Controllers\Api\V1\TeacherController;
use App\Http\Controllers\Api\V1\UserController;
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

    Route::post(
        'auth/teacher/login',
        [TeacherAuthController::class, 'login']
    )->middleware('throttle:5,1');

    Route::post('auth/student/login', [StudentAuthController::class, 'login'])->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | Protected routes
    |--------------------------------------------------------------------------
    */

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
                'teachers',
                TeacherController::class
            );

            Route::apiResource(
                'students',
                StudentController::class
            );

            Route::apiResource(
                'users',
                UserController::class
            );

            Route::get(
                '/permissions/options',
                [
                    PermissionController::class,
                    'options',
                ]
            );

            Route::apiResource('roles', RoleController::class);

            Route::apiResource('permissions', PermissionController::class);

            Route::apiResource('fee-types', FeeTypeController::class);

            Route::apiResource('invoices', InvoiceController::class);

            Route::apiResource('payments', PaymentController::class);

            Route::apiResource('education-units', EducationUnitController::class);

            Route::apiResource('semesters', SemesterController::class);

            Route::apiResource('school-classes', SchoolClassController::class);
        });

});
