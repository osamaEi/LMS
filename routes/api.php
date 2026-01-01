<?php

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

// Zoom Webhook (No authentication - Zoom will call this)
Route::post('/zoom/webhook', [App\Http\Controllers\Admin\ZoomWebhookController::class, 'handleWebhook']);

// API Version 1
Route::prefix('v1')->group(function () {

    // Public Routes
    Route::get('/programs', [App\Http\Controllers\Api\V1\ProgramController::class, 'index']);
    Route::get('/programs/{program}', [App\Http\Controllers\Api\V1\ProgramController::class, 'show']);

    // Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'register']);
        Route::post('/send-otp', [App\Http\Controllers\Api\V1\Auth\OtpController::class, 'send']);
        Route::post('/verify-otp', [App\Http\Controllers\Api\V1\Auth\OtpController::class, 'verify']);
        Route::post('/nafath/initiate', [App\Http\Controllers\Api\V1\Auth\NafathController::class, 'initiate']);
        Route::get('/nafath/poll/{transaction_id}', [App\Http\Controllers\Api\V1\Auth\NafathController::class, 'poll']);
        Route::post('/login', [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'login']);
        Route::post('/forgot-password', [App\Http\Controllers\Api\V1\Auth\PasswordResetController::class, 'forgot']);
        Route::post('/reset-password', [App\Http\Controllers\Api\V1\Auth\PasswordResetController::class, 'reset']);
    });

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth Routes (Protected)
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'logout']);
        });

        // Profile Routes
        Route::get('/profile', [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'profile']);
        Route::post('/profile/complete', [App\Http\Controllers\Api\V1\ProfileController::class, 'completeProfile']);

        // Track Routes
        Route::prefix('tracks')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\V1\TrackController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\V1\TrackController::class, 'store']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\TrackController::class, 'show']);
            Route::put('/{id}', [App\Http\Controllers\Api\V1\TrackController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\TrackController::class, 'destroy']);
            Route::get('/{id}/terms', [App\Http\Controllers\Api\V1\TrackController::class, 'terms']);
            Route::post('/{id}/assign-student', [App\Http\Controllers\Api\V1\TrackController::class, 'assignStudent']);
            Route::post('/promote-student', [App\Http\Controllers\Api\V1\TrackController::class, 'promoteStudent']);
        });

        // Student Routes
        Route::prefix('student')->group(function () {
            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'index']);
            Route::get('/my-subjects', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'mySubjects']);
            Route::get('/statistics', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'statistics']);
            Route::get('/upcoming-sessions', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'upcomingSessions']);
            Route::get('/attendance', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'myAttendance']);
            Route::get('/links', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'usefulLinks']);

            // Zoom Sessions - Join and Leave with Attendance Tracking
            Route::post('/sessions/{sessionId}/join-zoom', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'joinZoom']);
            Route::post('/sessions/{sessionId}/leave-zoom', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'leaveZoom']);

            // Subjects
            Route::get('/subjects/{id}', [App\Http\Controllers\Api\V1\Student\SubjectController::class, 'show']);
            Route::get('/units/{id}', [App\Http\Controllers\Api\V1\Student\SubjectController::class, 'showUnit']);
        });

        // Teacher Routes
        Route::prefix('teacher')->group(function () {
            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\Teacher\DashboardController::class, 'index']);
            Route::get('/my-subjects', [App\Http\Controllers\Api\V1\Teacher\DashboardController::class, 'mySubjects']);

            // Subjects Management
            Route::prefix('subjects')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\V1\Teacher\SubjectController::class, 'index']);
                Route::get('/{id}', [App\Http\Controllers\Api\V1\Teacher\SubjectController::class, 'show']);
                Route::get('/{id}/students', [App\Http\Controllers\Api\V1\Teacher\SubjectController::class, 'students']);
                Route::get('/{id}/statistics', [App\Http\Controllers\Api\V1\Teacher\SubjectController::class, 'statistics']);
            });

            // Sessions Management
            Route::prefix('sessions')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'store']);
                Route::get('/{id}', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'show']);
                Route::put('/{id}', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'update']);
                Route::delete('/{id}', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'destroy']);

                // Session Files
                Route::post('/{id}/files', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'addFile']);
                Route::put('/{sessionId}/files/{fileId}', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'updateFile']);
                Route::delete('/{sessionId}/files/{fileId}', [App\Http\Controllers\Api\V1\Teacher\SessionController::class, 'deleteFile']);
            });

            // Attendance Management
            Route::prefix('sessions/{sessionId}/attendance')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\V1\Teacher\AttendanceController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\V1\Teacher\AttendanceController::class, 'store']);
                Route::post('/bulk', [App\Http\Controllers\Api\V1\Teacher\AttendanceController::class, 'bulkStore']);
                Route::put('/{attendanceId}', [App\Http\Controllers\Api\V1\Teacher\AttendanceController::class, 'update']);
                Route::delete('/{attendanceId}', [App\Http\Controllers\Api\V1\Teacher\AttendanceController::class, 'destroy']);
            });

            // Evaluations Management
            Route::prefix('evaluations')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'store']);
                Route::get('/{id}', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'show']);
                Route::put('/{id}', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'update']);
                Route::delete('/{id}', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'destroy']);

                // Submissions
                Route::get('/{id}/submissions', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'submissions']);
                Route::post('/{evaluationId}/submissions/{submissionId}/grade', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'gradeSubmission']);
                Route::post('/{evaluationId}/submissions/bulk-grade', [App\Http\Controllers\Api\V1\Teacher\EvaluationController::class, 'bulkGrade']);
            });
        });

        // Admin Routes (Super Admin & Admin only)
        Route::prefix('admin')->group(function () {
            // Dashboard
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'index']);
            Route::get('/dashboard/charts', [App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'getChartData']);
            Route::get('/dashboard/overview', [App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'getOverview']);
            
            // Dashboard View Configuration
            Route::get('/dashboard-views', [App\Http\Controllers\Api\V1\Admin\DashboardViewController::class, 'getAvailableViews']);
            Route::post('/dashboard-view/{role}', [App\Http\Controllers\Api\V1\Admin\DashboardViewController::class, 'updateDashboardView']);

            // Users Management
            Route::prefix('users')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'store']);
                Route::get('/stats', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'stats']);
                Route::get('/{id}', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'show']);
                Route::put('/{id}', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'update']);
                Route::delete('/{id}', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'destroy']);
            });

            // Programs Management
            Route::apiResource('programs', App\Http\Controllers\Api\V1\Admin\ProgramController::class);

            // Subjects Management
            Route::apiResource('subjects', App\Http\Controllers\Api\V1\Admin\SubjectController::class);

            // Units Management
            Route::apiResource('units', App\Http\Controllers\Api\V1\Admin\UnitController::class);

            // Sessions Management
            Route::apiResource('sessions', App\Http\Controllers\Api\V1\Admin\SessionController::class);

            // Enrollments Management
            Route::apiResource('enrollments', App\Http\Controllers\Api\V1\Admin\EnrollmentController::class);

            // Attendance Management
            Route::apiResource('attendances', App\Http\Controllers\Api\V1\Admin\AttendanceController::class);

            // Evaluations Management
            Route::apiResource('evaluations', App\Http\Controllers\Api\V1\Admin\EvaluationController::class);

            // Zoom Management
            Route::prefix('zoom')->group(function () {
                Route::post('/create-meeting', [App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'createMeeting']);
                Route::get('/meeting/{meetingId}', [App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'getMeeting']);
                Route::delete('/meeting/{meetingId}', [App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'deleteMeeting']);
            });
        });
    });
});
