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

    // Public News
    Route::get('/news', function () {
        $news = \App\Models\News::active()
            ->latest('published_at')
            ->paginate(request('per_page', 10));
        return response()->json(['success' => true, 'data' => $news]);
    });

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

            // Sessions
            Route::get('/sessions', [App\Http\Controllers\Api\V1\Student\SessionController::class, 'index']);
            Route::post('/sessions/{sessionId}/join-zoom', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'joinZoom']);
            Route::post('/sessions/{sessionId}/leave-zoom', [App\Http\Controllers\Api\V1\Student\DashboardController::class, 'leaveZoom']);

            // Schedule (Calendar)
            Route::get('/schedule', [App\Http\Controllers\Api\V1\Student\ScheduleController::class, 'index']);

            // Subjects & Units
            Route::get('/subjects/{id}', [App\Http\Controllers\Api\V1\Student\SubjectController::class, 'show']);
            Route::get('/units/{id}', [App\Http\Controllers\Api\V1\Student\SubjectController::class, 'showUnit']);

            // Program
            Route::get('/my-program', [App\Http\Controllers\Api\V1\Student\ProgramController::class, 'show']);
            Route::post('/enroll-program', [App\Http\Controllers\Api\V1\Student\ProgramController::class, 'enroll']);

            // Quizzes
            Route::get('/subjects/{subjectId}/quizzes', [App\Http\Controllers\Api\V1\Student\QuizController::class, 'index']);
            Route::get('/subjects/{subjectId}/quizzes/{quizId}', [App\Http\Controllers\Api\V1\Student\QuizController::class, 'show']);
            Route::post('/subjects/{subjectId}/quizzes/{quizId}/start', [App\Http\Controllers\Api\V1\Student\QuizController::class, 'start']);
            Route::get('/subjects/{subjectId}/quizzes/{quizId}/take', [App\Http\Controllers\Api\V1\Student\QuizController::class, 'take']);
            Route::post('/subjects/{subjectId}/quizzes/{quizId}/submit', [App\Http\Controllers\Api\V1\Student\QuizController::class, 'submit']);
            Route::get('/subjects/{subjectId}/quizzes/{quizId}/result/{attemptId}', [App\Http\Controllers\Api\V1\Student\QuizController::class, 'result']);

            // Tickets (Support)
            Route::get('/tickets', [App\Http\Controllers\Api\V1\Student\TicketController::class, 'index']);
            Route::post('/tickets', [App\Http\Controllers\Api\V1\Student\TicketController::class, 'store']);
            Route::get('/tickets/{id}', [App\Http\Controllers\Api\V1\Student\TicketController::class, 'show']);
            Route::post('/tickets/{id}/reply', [App\Http\Controllers\Api\V1\Student\TicketController::class, 'reply']);

            // Surveys
            Route::get('/surveys', [App\Http\Controllers\Api\V1\Student\SurveyController::class, 'index']);
            Route::get('/surveys/{id}', [App\Http\Controllers\Api\V1\Student\SurveyController::class, 'show']);
            Route::post('/surveys/{id}/submit', [App\Http\Controllers\Api\V1\Student\SurveyController::class, 'submit']);

            // Teacher Ratings
            Route::get('/teacher-ratings', [App\Http\Controllers\Api\V1\Student\TeacherRatingController::class, 'index']);
            Route::get('/teacher-ratings/{subjectId}', [App\Http\Controllers\Api\V1\Student\TeacherRatingController::class, 'show']);
            Route::post('/teacher-ratings/{subjectId}', [App\Http\Controllers\Api\V1\Student\TeacherRatingController::class, 'store']);

            // Payments
            Route::get('/payments', [App\Http\Controllers\Api\V1\Student\PaymentController::class, 'index']);
            Route::get('/payments/{id}', [App\Http\Controllers\Api\V1\Student\PaymentController::class, 'show']);
            Route::post('/payments/{id}/pay-with-tamara', [App\Http\Controllers\Api\V1\Student\PaymentController::class, 'payWithTamara']);
            Route::post('/payments/{id}/pay-with-paytabs', [App\Http\Controllers\Api\V1\Student\PaymentController::class, 'payWithPayTabs']);

            // Notifications
            Route::prefix('notifications')->group(function () {
                Route::get('/', [App\Http\Controllers\NotificationController::class, 'index']);
                Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount']);
                Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead']);
                Route::post('/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
            });
        });



      
    });
});
