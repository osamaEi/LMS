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
    return view('front.training-paths');
})->name('training-paths');

Route::get('/short-courses', function () {
    return view('front.short-courses');
})->name('short-courses');

Route::get('/about', function () {
    return view('front.about');
})->name('about');

Route::get('/news', function () {
    return view('front.news');
})->name('news');

Route::get('/faq', function () {
    return view('front.faq');
})->name('faq');

Route::get('/contact', [\App\Http\Controllers\Front\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Front\ContactController::class, 'store'])->name('contact.store');

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

// Admin Routes
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Teachers Management
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);

    // Students Management
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);

    // Courses Management
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);

    // Program (Path) Management
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

    // Zoom Integration
    Route::post('/zoom/create-meeting', [\App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'createMeeting'])
        ->name('zoom.create-meeting');
    Route::post('/zoom/generate-signature', [\App\Http\Controllers\Api\V1\Admin\ZoomController::class, 'generateSignature'])
        ->name('zoom.generate-signature');

    // Reports
    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('reports');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

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
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('/users/{user}/remove-role/{role}', [UserManagementController::class, 'removeRole'])->name('users.remove-role');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/subjects/{id}', [TeacherDashboardController::class, 'showSubject'])->name('subjects.show');

    // Courses (TODO: Create controller)
    // Route::resource('courses', \App\Http\Controllers\Teacher\CourseController::class);

    // Schedule
    Route::get('/schedule', [\App\Http\Controllers\Teacher\ScheduleController::class, 'index'])->name('schedule');

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
    Route::get('/profile', function () {
        return view('teacher.profile');
    })->name('profile');

    // Surveys (NELC 1.2.11)
    Route::get('/surveys', [\App\Http\Controllers\Teacher\SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/{survey}', [\App\Http\Controllers\Teacher\SurveyController::class, 'show'])->name('surveys.show');
    Route::post('/surveys/{survey}', [\App\Http\Controllers\Teacher\SurveyController::class, 'submit'])->name('surveys.submit');

    // Support Tickets (NELC 1.3.3)
    Route::resource('tickets', \App\Http\Controllers\Teacher\TicketController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Teacher\TicketController::class, 'reply'])->name('tickets.reply');
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
    Route::get('/attendance', function () {
        return view('student.attendance.index');
    })->name('attendance.index');

    // Profile
    Route::get('/profile', function () {
        return view('student.profile');
    })->name('profile');

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
});
