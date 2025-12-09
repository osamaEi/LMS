<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Home route
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

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
    Route::delete('/session-files/{file}', [\App\Http\Controllers\Admin\SessionController::class, 'deleteFile'])
        ->name('sessions.files.delete');

    // Reports
    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('reports');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    // Courses (TODO: Create controller)
    // Route::resource('courses', \App\Http\Controllers\Teacher\CourseController::class);

    // Schedule
    Route::get('/schedule', function () {
        return view('teacher.schedule');
    })->name('schedule');

    // Students
    Route::get('/students', function () {
        return view('teacher.students.index');
    })->name('students.index');

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
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Courses
    Route::get('/courses', function () {
        return view('student.courses.index');
    })->name('courses.index');

    Route::get('/courses/{course}', function ($course) {
        return view('student.courses.show', compact('course'));
    })->name('courses.show');

    Route::get('/courses/browse', function () {
        return view('student.courses.browse');
    })->name('courses.browse');

    // Schedule
    Route::get('/schedule', function () {
        return view('student.schedule');
    })->name('schedule');

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
});
