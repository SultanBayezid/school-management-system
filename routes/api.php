<?php

use App\Http\Controllers\Api\V1\AttendanceApiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use App\Http\Controllers\Api\V1\ExamApiController;
use App\Http\Controllers\Api\V1\InvoiceApiController;
use App\Http\Controllers\Api\V1\LibraryApiController;
use App\Http\Controllers\Api\V1\NoticeApiController;
use App\Http\Controllers\Api\V1\SchoolClassApiController;
use App\Http\Controllers\Api\V1\StudentApiController;
use App\Http\Controllers\Api\V1\TeacherApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public.
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/stats', [DashboardApiController::class, 'stats']);

        Route::get('/students', [StudentApiController::class, 'index']);
        Route::get('/students/{student}', [StudentApiController::class, 'show']);

        Route::get('/teachers', [TeacherApiController::class, 'index']);
        Route::get('/teachers/{teacher}', [TeacherApiController::class, 'show']);

        Route::get('/classes', [SchoolClassApiController::class, 'index']);
        Route::get('/classes/{class}', [SchoolClassApiController::class, 'show']);

        Route::get('/attendance', [AttendanceApiController::class, 'index']);
        Route::post('/attendance', [AttendanceApiController::class, 'store'])
            ->middleware('role:admin,teacher');

        Route::get('/exams', [ExamApiController::class, 'index']);
        Route::get('/exams/{exam}', [ExamApiController::class, 'show']);
        Route::get('/exams/{exam}/report-card/{student}', [ExamApiController::class, 'reportCard']);

        Route::get('/invoices', [InvoiceApiController::class, 'index']);
        Route::get('/invoices/{invoice}', [InvoiceApiController::class, 'show']);

        Route::get('/notices', [NoticeApiController::class, 'index']);

        Route::get('/library/books', [LibraryApiController::class, 'index']);
    });
});
