<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// My IP (for Nafath whitelist)
Route::get('/my-ip', function () {
    return response()->json(['ip' => request()->ip()]);
});

// Registration Routes
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showForm'])->name('register');
Route::post('/register/nafath', [\App\Http\Controllers\Auth\RegisterController::class, 'initiateNafath'])->name('register.nafath');
Route::get('/register/nafath/poll/{transactionId}', [\App\Http\Controllers\Auth\RegisterController::class, 'pollNafath'])->name('register.nafath.poll');
Route::post('/register/complete', [\App\Http\Controllers\Auth\RegisterController::class, 'completeRegistration'])->name('register.complete');

// Zoom OAuth Callback (for General App setup - not actively used)
Route::get('/oauth/callback', function () {
    return response()->json([
        'message' => 'Zoom OAuth callback received',
        'note' => 'This endpoint is only required for Zoom App configuration. The LMS uses SDK credentials for joining meetings.',
    ]);
})->name('zoom.oauth.callback');

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Front pages routes
Route::get('/training-paths', function () {
    $programs = \App\Models\Program::where('status', 'active')->get();
    return view('front.training-paths', compact('programs'));
})->name('training-paths');

Route::get('/short-courses', function () {
    $programs = \App\Models\Program::where('status', 'active')->get();
    return view('front.short-courses', compact('programs'));
})->name('short-courses');

Route::get('/about', function () {
    return view('front.about');
})->name('about');

Route::get('/news', [\App\Http\Controllers\Front\NewsController::class, 'index'])->name('news');

Route::get('/faq', function () {
    return view('front.faq');
})->name('faq');

Route::get('/contact', [\App\Http\Controllers\Front\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Front\ContactController::class, 'store'])->name('contact.store');

// NELC Public Pages (Policies, Guides, Open Content)
Route::prefix('nelc')->name('nelc.')->group(function () {
    // Policy Pages (Public - no auth required per NELC standards)
    Route::prefix('policies')->name('policies.')->group(function () {
        Route::get('/privacy', fn() => view('front.policies.privacy'))->name('privacy');
        Route::get('/academic-integrity', fn() => view('front.policies.academic-integrity'))->name('academic-integrity');
        Route::get('/intellectual-property', fn() => view('front.policies.intellectual-property'))->name('intellectual-property');
        Route::get('/communication', fn() => view('front.policies.communication'))->name('communication');
        Route::get('/attendance', fn() => view('front.policies.attendance'))->name('attendance');
        Route::get('/assessment', fn() => view('front.policies.assessment'))->name('assessment');
        Route::get('/ai-ethics', fn() => view('front.policies.ai-ethics'))->name('ai-ethics');
        Route::get('/technical-support', fn() => view('front.policies.technical-support'))->name('technical-support');
        Route::get('/accessibility', fn() => view('front.policies.accessibility'))->name('accessibility');
        Route::get('/national-compliance', fn() => view('front.policies.national-compliance'))->name('national-compliance');
        Route::get('/risk-management', fn() => view('front.policies.risk-management'))->name('risk-management');
    });

    // User Guides (Public - accessible without login per NELC 1.3.2)
    Route::prefix('guides')->name('guides.')->group(function () {
        Route::get('/student', fn() => view('front.guides.student'))->name('student');
        Route::get('/teacher', fn() => view('front.guides.teacher'))->name('teacher');
    });

    // Open Educational Content (Public - per NELC 2.1.5)
    Route::get('/open-content', fn() => view('front.open-content'))->name('open-content');
});

// Language Switch Route
Route::get('/lang/{locale}', [\App\Http\Controllers\LangController::class, 'switch'])->name('lang.switch');

// Redirect authenticated users to their role-specific dashboard
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'admin', 'super_admin' => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('teacher.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware('auth')->name('dashboard');

// Notification Routes
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/page', [\App\Http\Controllers\NotificationController::class, 'page'])->name('page');
    Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::post('/send', [\App\Http\Controllers\NotificationController::class, 'send'])->name('send')->middleware('role:admin,super-admin,super_admin');
});

// Profile Routes (All authenticated users)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('update-password');
    Route::post('/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('update-avatar');
});

// Admin Routes
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Teachers Management
    Route::get('/teachers/export', [\App\Http\Controllers\Admin\TeacherController::class, 'export'])->name('teachers.export');
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);

    // Students Management
    Route::get('/students/export', [\App\Http\Controllers\Admin\StudentController::class, 'export'])->name('students.export');
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::post('/students/{student}/assign-program', [\App\Http\Controllers\Admin\StudentController::class, 'assignProgram'])
        ->name('students.assign-program');
    Route::delete('/students/{student}/remove-program', [\App\Http\Controllers\Admin\StudentController::class, 'removeProgram'])
        ->name('students.remove-program');
    Route::post('/students/{student}/toggle-status', [\App\Http\Controllers\Admin\StudentController::class, 'toggleStatus'])
        ->name('students.toggle-status');

    // Program Enrollments (Approval)
    Route::prefix('program-enrollments')->name('program-enrollments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProgramEnrollmentController::class, 'index'])->name('index');
        Route::get('/{user}', [\App\Http\Controllers\Admin\ProgramEnrollmentController::class, 'show'])->name('show');
        Route::post('/{user}/approve', [\App\Http\Controllers\Admin\ProgramEnrollmentController::class, 'approve'])->name('approve');
        Route::delete('/{user}/reject', [\App\Http\Controllers\Admin\ProgramEnrollmentController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [\App\Http\Controllers\Admin\ProgramEnrollmentController::class, 'bulkApprove'])->name('bulk-approve');
        Route::delete('/bulk-reject', [\App\Http\Controllers\Admin\ProgramEnrollmentController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Program (Path) Management
    Route::get('/programs/export', [\App\Http\Controllers\Admin\ProgramController::class, 'export'])->name('programs.export');
    Route::resource('programs', \App\Http\Controllers\Admin\ProgramController::class);

    // Term (Semester) Management
    Route::resource('terms', \App\Http\Controllers\Admin\TermController::class);

    // Subject Management
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);

    // Session (Lesson) Management
    Route::resource('sessions', \App\Http\Controllers\Admin\SessionController::class);
    Route::get('/sessions/{session}/zoom', [\App\Http\Controllers\Admin\SessionController::class, 'showZoom'])
        ->name('sessions.zoom');
    Route::get('/sessions/{session}/zoom-dashboard', [\App\Http\Controllers\Admin\SessionController::class, 'showZoomDashboard'])
        ->name('sessions.zoom-dashboard');
    Route::delete('/session-files/{file}', [\App\Http\Controllers\Admin\SessionController::class, 'deleteFile'])
        ->name('sessions.files.delete');
    Route::post('/sessions/store-batch', [\App\Http\Controllers\Admin\SessionController::class, 'storeBatch'])
        ->name('sessions.store-batch');
    Route::post('/sessions/{session}/reschedule', [\App\Http\Controllers\Admin\SessionController::class, 'reschedule'])
        ->name('sessions.reschedule');

    // Zoom Integration
    Route::post('/zoom/create-meeting', [\App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'createMeeting'])
        ->name('zoom.create-meeting');
    Route::post('/zoom/generate-signature', [\App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'generateSignature'])
        ->name('zoom.generate-signature');

    // Recording Management
    Route::get('/recordings', [\App\Http\Controllers\Admin\RecordingController::class, 'index'])->name('recordings.index');
    Route::post('/recordings/{session}/sync', [\App\Http\Controllers\Admin\RecordingController::class, 'sync'])->name('recordings.sync');
    Route::delete('/recordings/{session}', [\App\Http\Controllers\Admin\RecordingController::class, 'destroy'])->name('recordings.destroy');
    Route::delete('/recordings/{session}/zoom', [\App\Http\Controllers\Admin\RecordingController::class, 'deleteFromZoom'])->name('recordings.delete-zoom');
    Route::get('/recordings/{session}/download', [\App\Http\Controllers\Admin\RecordingController::class, 'download'])->name('recordings.download');

    // Reports (NELC Compliance)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/nelc-compliance', [\App\Http\Controllers\Admin\ReportController::class, 'nelcCompliance'])->name('nelc-compliance');
        Route::get('/student-progress', [\App\Http\Controllers\Admin\ReportController::class, 'studentProgress'])->name('student-progress');
        Route::get('/attendance', [\App\Http\Controllers\Admin\ReportController::class, 'attendance'])->name('attendance');
        Route::get('/grades', [\App\Http\Controllers\Admin\ReportController::class, 'grades'])->name('grades');
        Route::get('/teacher-performance', [\App\Http\Controllers\Admin\ReportController::class, 'teacherPerformance'])->name('teacher-performance');
        Route::get('/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/group/{group}', [\App\Http\Controllers\Admin\SettingController::class, 'updateGroup'])->name('settings.update-group');
    Route::post('/settings/clear-cache', [\App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');
    Route::post('/settings/test-email', [\App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('settings.test-email');
    Route::post('/settings/maintenance', [\App\Http\Controllers\Admin\SettingController::class, 'maintenance'])->name('settings.maintenance');

    // Surveys Management (NELC 1.2.11)
    Route::resource('surveys', \App\Http\Controllers\Admin\SurveyController::class);
    Route::get('/surveys/{survey}/report', [\App\Http\Controllers\Admin\SurveyController::class, 'report'])->name('surveys.report');

    // Tickets/Support Management (NELC 1.3.3)
    Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class)->only(['index', 'show']);
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::patch('/tickets/{ticket}/assign', [\App\Http\Controllers\Admin\TicketController::class, 'assign'])->name('tickets.assign');
    Route::patch('/tickets/{ticket}/priority', [\App\Http\Controllers\Admin\TicketController::class, 'updatePriority'])->name('tickets.priority');

    // Teacher Ratings Management (NELC 2.4.9)
    Route::get('/teacher-ratings', [\App\Http\Controllers\Admin\TeacherRatingController::class, 'index'])->name('teacher-ratings.index');
    Route::get('/teacher-ratings/report', [\App\Http\Controllers\Admin\TeacherRatingController::class, 'report'])->name('teacher-ratings.report');
    Route::get('/teacher-ratings/{teacher}', [\App\Http\Controllers\Admin\TeacherRatingController::class, 'show'])->name('teacher-ratings.show');
    Route::post('/teacher-ratings/{rating}/approve', [\App\Http\Controllers\Admin\TeacherRatingController::class, 'approve'])->name('teacher-ratings.approve');
    Route::delete('/teacher-ratings/{rating}/reject', [\App\Http\Controllers\Admin\TeacherRatingController::class, 'reject'])->name('teacher-ratings.reject');

    // Roles Management (Spatie)
    Route::resource('roles', RoleController::class);

    // Permissions Management (Spatie)
    Route::resource('permissions', PermissionController::class)->except(['show']);
    Route::get('/permissions/bulk-create', [PermissionController::class, 'bulkCreate'])->name('permissions.bulk-create');
    Route::post('/permissions/bulk-store', [PermissionController::class, 'bulkStore'])->name('permissions.bulk-store');

    // Users Management with Role Assignment
    Route::get('/users/export', [UserManagementController::class, 'export'])->name('users.export');
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('/users/{user}/remove-role/{role}', [UserManagementController::class, 'removeRole'])->name('users.remove-role');

    // Payments Management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');

        // Installment plan
        Route::post('/{payment}/installment-plan', [\App\Http\Controllers\Admin\PaymentController::class, 'createInstallmentPlan'])->name('installment-plan');

        // Record payments
        Route::post('/{payment}/record-payment', [\App\Http\Controllers\Admin\PaymentController::class, 'recordPayment'])->name('record-payment');
        Route::post('/installments/{installment}/record-payment', [\App\Http\Controllers\Admin\PaymentController::class, 'recordInstallmentPayment'])->name('installment.record-payment');

        // Actions
        Route::post('/{payment}/update', [\App\Http\Controllers\Admin\PaymentController::class, 'update'])->name('update');
        Route::post('/{payment}/refund', [\App\Http\Controllers\Admin\PaymentController::class, 'refund'])->name('refund');
        Route::post('/{payment}/waive', [\App\Http\Controllers\Admin\PaymentController::class, 'waive'])->name('waive');
        Route::post('/{payment}/cancel', [\App\Http\Controllers\Admin\PaymentController::class, 'cancel'])->name('cancel');

        // Overdue
        Route::get('/overdue/installments', [\App\Http\Controllers\Admin\PaymentController::class, 'overdueInstallments'])->name('overdue');
    });

    // News Management
    Route::resource('news', \App\Http\Controllers\Admin\NewsController::class)->except(['show']);
    Route::post('/news/{news}/toggle-status', [\App\Http\Controllers\Admin\NewsController::class, 'toggleStatus'])->name('news.toggle-status');

    // Contact Requests Management
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('index');
        Route::get('/{contact}', [\App\Http\Controllers\Admin\ContactController::class, 'show'])->name('show');
        Route::patch('/{contact}/status', [\App\Http\Controllers\Admin\ContactController::class, 'updateStatus'])->name('update-status');
        Route::post('/{contact}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('reply');
        Route::delete('/{contact}', [\App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('destroy');
    });

    // Activity Logs (NELC Compliance)
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('index');
        Route::get('/stats', [\App\Http\Controllers\Admin\ActivityLogController::class, 'stats'])->name('stats');
        Route::get('/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('export');
        Route::get('/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('show');
    });

    // NELC Compliance Dashboard
    Route::get('/nelc-compliance', [\App\Http\Controllers\Admin\NelcComplianceController::class, 'dashboard'])->name('nelc-compliance');

    // xAPI Dashboard (NELC Compliance)
    Route::prefix('xapi')->name('xapi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\XapiController::class, 'index'])->name('index');
        Route::post('/test-connection', [\App\Http\Controllers\Admin\XapiController::class, 'testConnection'])->name('test-connection');
        Route::post('/process-pending', [\App\Http\Controllers\Admin\XapiController::class, 'processPending'])->name('process-pending');
    });
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/subjects/{id}', [TeacherDashboardController::class, 'showSubject'])->name('subjects.show');

    // My Subjects (Teacher's own subjects management)
    Route::get('/my-subjects', [\App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('my-subjects.index');
    Route::get('/my-subjects/{id}', [\App\Http\Controllers\Teacher\SubjectController::class, 'show'])->name('my-subjects.show');

    // Sessions Management (under my-subjects)
    Route::get('/my-subjects/{subjectId}/sessions/create', [\App\Http\Controllers\Teacher\SubjectController::class, 'createSession'])->name('my-subjects.sessions.create');
    Route::post('/my-subjects/{subjectId}/sessions', [\App\Http\Controllers\Teacher\SubjectController::class, 'storeSession'])->name('my-subjects.sessions.store');
    Route::get('/my-subjects/{subjectId}/sessions/{sessionId}/edit', [\App\Http\Controllers\Teacher\SubjectController::class, 'editSession'])->name('my-subjects.sessions.edit');
    Route::put('/my-subjects/{subjectId}/sessions/{sessionId}', [\App\Http\Controllers\Teacher\SubjectController::class, 'updateSession'])->name('my-subjects.sessions.update');
    Route::delete('/my-subjects/{subjectId}/sessions/{sessionId}', [\App\Http\Controllers\Teacher\SubjectController::class, 'destroySession'])->name('my-subjects.sessions.destroy');
    Route::get('/my-subjects/{subjectId}/sessions/{sessionId}/zoom', [\App\Http\Controllers\Teacher\SubjectController::class, 'showZoom'])->name('my-subjects.sessions.zoom');
    Route::get('/my-subjects/{subjectId}/sessions/{sessionId}/zoom-embedded', [\App\Http\Controllers\Teacher\SubjectController::class, 'showZoomEmbedded'])->name('my-subjects.sessions.zoom-embedded');
    Route::get('/my-subjects/{subjectId}/sessions/{sessionId}/attendance', [\App\Http\Controllers\Teacher\SubjectController::class, 'sessionAttendance'])->name('my-subjects.sessions.attendance');
    Route::delete('/my-subjects/{subjectId}/sessions/{sessionId}/files/{fileId}', [\App\Http\Controllers\Teacher\SubjectController::class, 'deleteSessionFile'])->name('my-subjects.sessions.files.destroy');

    // Simple session show route for teachers (for calendar clicks)
    Route::get('/sessions/{session}', [\App\Http\Controllers\Teacher\SessionController::class, 'show'])->name('sessions.show');

    // Zoom signature generation for teachers
    Route::post('/zoom/generate-signature', [\App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'generateSignature'])->name('zoom.generate-signature');

    // Schedule
    Route::get('/schedule', [\App\Http\Controllers\Teacher\ScheduleController::class, 'index'])->name('schedule');
    Route::post('/schedule/sessions', [\App\Http\Controllers\Teacher\ScheduleController::class, 'bulkStore'])->name('schedule.sessions.store');

    // Students
    Route::get('/students', [\App\Http\Controllers\Teacher\StudentsController::class, 'index'])->name('students.index');

    // Attendance
    Route::get('/attendance', function () {
        return view('teacher.attendance.index');
    })->name('attendance.index');

    // Assignments (TODO: Create controller)
    // Route::resource('assignments', \App\Http\Controllers\Teacher\AssignmentController::class);

    // Grades
    Route::get('/grades', function () {
        return view('teacher.grades.index');
    })->name('grades.index');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile');

    // Surveys (NELC 1.2.11)
    Route::get('/surveys', [\App\Http\Controllers\Teacher\SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Teacher\SurveyController::class, 'show'])->name('surveys.show');
    Route::post('/surveys/{survey}', [\App\Http\Controllers\Teacher\SurveyController::class, 'submit'])->name('surveys.submit');

    // Support Tickets (NELC 1.3.3)
    Route::resource('tickets', \App\Http\Controllers\Teacher\TicketController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Teacher\TicketController::class, 'reply'])->name('tickets.reply');

    // Files & Resources
    Route::get('/files', [\App\Http\Controllers\Teacher\FileController::class, 'index'])->name('files.index');

    // Quizzes & Exams Management
    Route::prefix('subjects/{subject}/quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\QuizController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\QuizController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\QuizController::class, 'store'])->name('store');
        Route::get('/{quiz}', [\App\Http\Controllers\Teacher\QuizController::class, 'show'])->name('show');
        Route::get('/{quiz}/edit', [\App\Http\Controllers\Teacher\QuizController::class, 'edit'])->name('edit');
        Route::put('/{quiz}', [\App\Http\Controllers\Teacher\QuizController::class, 'update'])->name('update');
        Route::delete('/{quiz}', [\App\Http\Controllers\Teacher\QuizController::class, 'destroy'])->name('destroy');
        Route::get('/{quiz}/results', [\App\Http\Controllers\Teacher\QuizController::class, 'results'])->name('results');
        Route::get('/{quiz}/results/{attempt}', [\App\Http\Controllers\Teacher\QuizController::class, 'gradeAttempt'])->name('grade-attempt');
        Route::post('/{quiz}/results/{attempt}/grade', [\App\Http\Controllers\Teacher\QuizController::class, 'submitGrade'])->name('submit-grade');

        // Questions Management
        Route::get('/{quiz}/questions/create', [\App\Http\Controllers\Teacher\QuizController::class, 'createQuestion'])->name('questions.create');
        Route::post('/{quiz}/questions', [\App\Http\Controllers\Teacher\QuizController::class, 'storeQuestion'])->name('questions.store');
        Route::get('/{quiz}/questions/{question}/edit', [\App\Http\Controllers\Teacher\QuizController::class, 'editQuestion'])->name('questions.edit');
        Route::put('/{quiz}/questions/{question}', [\App\Http\Controllers\Teacher\QuizController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/{quiz}/questions/{question}', [\App\Http\Controllers\Teacher\QuizController::class, 'destroyQuestion'])->name('questions.destroy');
    });
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/subjects/{id}', [\App\Http\Controllers\Student\DashboardController::class, 'showSubject'])->name('subjects.show');

    // Schedule
    Route::get('/schedule', [\App\Http\Controllers\Student\ScheduleController::class, 'index'])->name('schedule');

    // Assignments
    Route::get('/assignments', function () {
        return view('student.assignments.index');
    })->name('assignments.index');

    // Grades
    Route::get('/grades', function () {
        return view('student.grades.index');
    })->name('grades.index');

    // Attendance
    Route::get('/attendance', [\App\Http\Controllers\Student\DashboardController::class, 'attendance'])->name('attendance');

    // My Sessions (All sessions for enrolled subjects)
    Route::get('/my-sessions', [\App\Http\Controllers\Student\DashboardController::class, 'mySessions'])->name('my-sessions');

    // Upcoming Sessions & Zoom
    Route::get('/upcoming-sessions', [\App\Http\Controllers\Student\DashboardController::class, 'upcomingSessions'])->name('upcoming-sessions');
    Route::get('/sessions/{sessionId}/join-zoom', [\App\Http\Controllers\Student\DashboardController::class, 'joinZoom'])->name('sessions.join-zoom');
    Route::post('/sessions/{sessionId}/leave-zoom', [\App\Http\Controllers\Student\DashboardController::class, 'leaveZoom'])->name('sessions.leave-zoom');

    // Files & Resources
    Route::get('/files', [\App\Http\Controllers\Student\FileController::class, 'index'])->name('files.index');

    // Useful Links
    Route::get('/links', [\App\Http\Controllers\Student\DashboardController::class, 'usefulLinks'])->name('links');
    Route::get('/links/{service}', [\App\Http\Controllers\Student\DashboardController::class, 'showLink'])
         ->name('links.show')
         ->where('service', 'portal|library|blackboard|calendar|support|schedule');

    // My Program (البرنامج الدراسي)
    Route::get('/my-program', [\App\Http\Controllers\Student\DashboardController::class, 'myProgram'])->name('my-program');
    Route::post('/enroll-program', [\App\Http\Controllers\Student\DashboardController::class, 'enrollInProgram'])->name('enroll-program');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile');

    // Surveys (NELC 1.2.11)
    Route::get('/surveys', [\App\Http\Controllers\Student\SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Student\SurveyController::class, 'show'])->name('surveys.show');
    Route::post('/surveys/{survey}', [\App\Http\Controllers\Student\SurveyController::class, 'submit'])->name('surveys.submit');

    // Support Tickets (NELC 1.3.3)
    Route::resource('tickets', \App\Http\Controllers\Student\TicketController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Student\TicketController::class, 'reply'])->name('tickets.reply');

    // Teacher Ratings (NELC 2.4.9)
    Route::get('/teacher-ratings', [\App\Http\Controllers\Student\TeacherRatingController::class, 'index'])->name('teacher-ratings.index');
    Route::get('/teacher-ratings/{subject}/create', [\App\Http\Controllers\Student\TeacherRatingController::class, 'create'])->name('teacher-ratings.create');
    Route::post('/teacher-ratings/{subject}', [\App\Http\Controllers\Student\TeacherRatingController::class, 'store'])->name('teacher-ratings.store');

    // Quizzes & Exams
    Route::prefix('subjects/{subject}/quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\QuizController::class, 'index'])->name('index');
        Route::get('/{quiz}', [\App\Http\Controllers\Student\QuizController::class, 'show'])->name('show');
        Route::post('/{quiz}/start', [\App\Http\Controllers\Student\QuizController::class, 'start'])->name('start');
        Route::get('/{quiz}/take', [\App\Http\Controllers\Student\QuizController::class, 'take'])->name('take');
        Route::post('/{quiz}/submit', [\App\Http\Controllers\Student\QuizController::class, 'submit'])->name('submit');
        Route::get('/{quiz}/result/{attempt}', [\App\Http\Controllers\Student\QuizController::class, 'result'])->name('result');
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [\App\Http\Controllers\Student\PaymentController::class, 'show'])->name('show');

        // Tamara Integration
        Route::post('/{payment}/pay-with-tamara', [\App\Http\Controllers\Student\PaymentController::class, 'payWithTamara'])->name('pay-tamara');
        Route::get('/tamara/return', [\App\Http\Controllers\Student\PaymentController::class, 'tamaraReturn'])->name('tamara.return');
        Route::get('/tamara/cancel', [\App\Http\Controllers\Student\PaymentController::class, 'tamaraCancel'])->name('tamara.cancel');

        // PayTabs Integration (Credit Card / Apple Pay)
        Route::post('/{payment}/pay-with-paytabs', [\App\Http\Controllers\Student\PaymentController::class, 'payWithPayTabs'])->name('pay-paytabs');
        Route::get('/paytabs/return', [\App\Http\Controllers\Student\PaymentController::class, 'payTabsReturn'])->name('paytabs.return');

        // Stripe Integration
        Route::post('/{payment}/pay-with-stripe', [\App\Http\Controllers\Student\PaymentController::class, 'payWithStripe'])->name('pay-stripe');
        Route::get('/stripe/success', [\App\Http\Controllers\Student\PaymentController::class, 'stripeSuccess'])->name('stripe.success');
        Route::get('/stripe/cancel', [\App\Http\Controllers\Student\PaymentController::class, 'stripeCancel'])->name('stripe.cancel');
    });
});

// Webhooks (Public routes - no authentication required)
Route::post('/webhooks/tamara', [\App\Http\Controllers\Webhooks\TamaraWebhookController::class, 'handle'])->name('webhooks.tamara');
Route::post('/webhooks/paytabs', [\App\Http\Controllers\Student\PaymentController::class, 'payTabsCallback'])->name('webhooks.paytabs');
Route::post('/webhooks/nafath', [\App\Http\Controllers\Webhooks\NafathWebhookController::class, 'handle'])->name('webhooks.nafath');

// Local Mock Nafath API (for development/testing)
Route::prefix('mock-nafath/api/v1/mfa')->group(function () {
    Route::post('/request', [\App\Http\Controllers\Mock\NafathMockController::class, 'createRequest']);
    Route::post('/request/status', [\App\Http\Controllers\Mock\NafathMockController::class, 'getStatus']);
    Route::get('/request/status/{transId}', [\App\Http\Controllers\Mock\NafathMockController::class, 'getStatus']);
    Route::get('/jwk', [\App\Http\Controllers\Mock\NafathMockController::class, 'getJwk']);
});
