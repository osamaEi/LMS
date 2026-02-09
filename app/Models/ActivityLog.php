<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'action_category',
        'loggable_type',
        'loggable_id',
        'properties',
        'ip_address',
        'user_agent',
        'session_id',
        'xapi_sent',
        'xapi_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'xapi_sent' => 'boolean',
            'xapi_sent_at' => 'datetime',
        ];
    }

    // ===========================
    // Relationships
    // ===========================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    // ===========================
    // Scopes
    // ===========================

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('action_category', $category);
    }

    public function scopeNotSentToXapi($query)
    {
        return $query->where('xapi_sent', false);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    // ===========================
    // Action Constants
    // ===========================

    // Auth
    const AUTH_LOGIN = 'login';
    const AUTH_LOGOUT = 'logout';
    const AUTH_REGISTER = 'register';
    const AUTH_PASSWORD_RESET = 'password_reset';

    // Content
    const CONTENT_VIEW_SESSION = 'view_session';
    const CONTENT_VIEW_RECORDING = 'view_recording';
    const CONTENT_DOWNLOAD_FILE = 'download_file';
    const CONTENT_VIEW_SUBJECT = 'view_subject';
    const CONTENT_VIEW_UNIT = 'view_unit';

    // Assessment
    const ASSESSMENT_START_QUIZ = 'start_quiz';
    const ASSESSMENT_SUBMIT_QUIZ = 'submit_quiz';
    const ASSESSMENT_VIEW_QUIZ_RESULT = 'view_quiz_result';
    const ASSESSMENT_SUBMIT_ASSIGNMENT = 'submit_assignment';
    const ASSESSMENT_GRADE = 'grade_evaluation';

    // Attendance
    const ATTENDANCE_JOIN = 'join_session';
    const ATTENDANCE_LEAVE = 'leave_session';
    const ATTENDANCE_MARK = 'mark_attendance';

    // Enrollment
    const ENROLLMENT_ENROLL = 'enroll';
    const ENROLLMENT_WITHDRAW = 'withdraw';
    const ENROLLMENT_COMPLETE = 'complete_course';

    // Communication
    const SURVEY_SUBMIT = 'submit_survey';
    const RATING_SUBMIT = 'submit_rating';
    const TICKET_CREATE = 'create_ticket';
    const TICKET_REPLY = 'reply_ticket';

    // Admin
    const ADMIN_CREATE_USER = 'create_user';
    const ADMIN_UPDATE_USER = 'update_user';
    const ADMIN_DELETE_USER = 'delete_user';
    const ADMIN_CREATE_PROGRAM = 'create_program';
    const ADMIN_UPDATE_PROGRAM = 'update_program';
    const ADMIN_DELETE_PROGRAM = 'delete_program';
    const ADMIN_CREATE_SESSION = 'create_session';
    const ADMIN_UPDATE_SESSION = 'update_session';
    const ADMIN_DELETE_SESSION = 'delete_session';

    // Navigation
    const NAV_PAGE_VIEW = 'page_view';

    // ===========================
    // Category Constants
    // ===========================
    const CATEGORY_AUTH = 'auth';
    const CATEGORY_CONTENT = 'content';
    const CATEGORY_ASSESSMENT = 'assessment';
    const CATEGORY_ATTENDANCE = 'attendance';
    const CATEGORY_ENROLLMENT = 'enrollment';
    const CATEGORY_COMMUNICATION = 'communication';
    const CATEGORY_ADMIN = 'admin';
    const CATEGORY_NAVIGATION = 'navigation';

    // ===========================
    // Helper Methods
    // ===========================

    /**
     * Mark this activity as sent to xAPI
     */
    public function markAsXapiSent(): void
    {
        $this->update([
            'xapi_sent' => true,
            'xapi_sent_at' => now(),
        ]);
    }

    /**
     * Get a human-readable description of this activity
     */
    public function getDescription(): string
    {
        $locale = app()->getLocale();

        $descriptions = [
            'ar' => [
                self::AUTH_LOGIN => 'تسجيل دخول',
                self::AUTH_LOGOUT => 'تسجيل خروج',
                self::AUTH_REGISTER => 'تسجيل حساب جديد',
                self::CONTENT_VIEW_SESSION => 'مشاهدة جلسة',
                self::CONTENT_DOWNLOAD_FILE => 'تحميل ملف',
                self::ASSESSMENT_START_QUIZ => 'بدء اختبار',
                self::ASSESSMENT_SUBMIT_QUIZ => 'إرسال اختبار',
                self::ATTENDANCE_JOIN => 'الانضمام لجلسة',
                self::ATTENDANCE_LEAVE => 'مغادرة جلسة',
                self::ENROLLMENT_ENROLL => 'التسجيل في مادة',
                self::SURVEY_SUBMIT => 'إرسال استبيان',
                self::RATING_SUBMIT => 'تقييم معلم',
                self::TICKET_CREATE => 'إنشاء تذكرة دعم',
            ],
            'en' => [
                self::AUTH_LOGIN => 'Login',
                self::AUTH_LOGOUT => 'Logout',
                self::AUTH_REGISTER => 'Register',
                self::CONTENT_VIEW_SESSION => 'View Session',
                self::CONTENT_DOWNLOAD_FILE => 'Download File',
                self::ASSESSMENT_START_QUIZ => 'Start Quiz',
                self::ASSESSMENT_SUBMIT_QUIZ => 'Submit Quiz',
                self::ATTENDANCE_JOIN => 'Join Session',
                self::ATTENDANCE_LEAVE => 'Leave Session',
                self::ENROLLMENT_ENROLL => 'Enroll in Subject',
                self::SURVEY_SUBMIT => 'Submit Survey',
                self::RATING_SUBMIT => 'Rate Teacher',
                self::TICKET_CREATE => 'Create Support Ticket',
            ],
        ];

        return $descriptions[$locale][$this->action] ?? $this->action;
    }
}
