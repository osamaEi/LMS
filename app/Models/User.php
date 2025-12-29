<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'national_id',
        'date_of_birth',
        'gender',
        'type',
        'program_id',
        'track_id',
        'current_term_number',
        'date_of_register',
        'is_terms',
        'is_confirm_user',
        'role',
        'status',
        'profile_photo',
        'bio',
        'specialization',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'nafath_verified_at' => 'datetime',
            'profile_completed_at' => 'datetime',
            'date_of_birth' => 'date',
            'date_of_register' => 'date',
            'is_terms' => 'boolean',
            'is_confirm_user' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role type (legacy method using role column)
     */
    public function hasRoleType(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is a teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Relationships
     */

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class);
    }

    public function track()
    {
        return $this->belongsTo(\App\Models\Track::class);
    }

    public function documents()
    {
        return $this->hasMany(\App\Models\StudentDocument::class);
    }

    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class, 'student_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'receiver_id');
    }

    public function tickets()
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'assigned_to');
    }

    public function surveyResponses()
    {
        return $this->hasMany(\App\Models\SurveyResponse::class);
    }

    // For teachers - ratings received
    public function ratingsReceived()
    {
        return $this->hasMany(\App\Models\TeacherRating::class, 'teacher_id');
    }

    // For students - ratings given
    public function ratingsGiven()
    {
        return $this->hasMany(\App\Models\TeacherRating::class, 'student_id');
    }

    /**
     * Get teacher's average rating
     */
    public function getAverageRating(): float
    {
        return \App\Models\TeacherRating::getTeacherAverageRating($this->id);
    }

    /**
     * Get teacher's ratings breakdown
     */
    public function getRatingsBreakdown(): array
    {
        return \App\Models\TeacherRating::getTeacherRatingsBreakdown($this->id);
    }

    // For teachers
    public function subjects()
    {
        return $this->hasMany(\App\Models\Subject::class, 'teacher_id');
    }

    // For students - alias for enrollments
    public function students()
    {
        return $this->hasMany(\App\Models\User::class, 'program_id');
    }

    // For teachers - courses taught by this teacher
    public function courses()
    {
        return $this->hasMany(\App\Models\Course::class, 'teacher_id');
    }

    /**
     * Get role display name in Arabic
     */
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'admin' => 'المدير',
            'teacher' => 'أستاذ',
            'student' => 'طالب',
            'super_admin' => 'المدير العام',
            default => 'مستخدم',
        };
    }

    /**
     * Get status display name in Arabic
     */
    public function getStatusDisplayName(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
            'pending' => 'قيد المراجعة',
            default => 'غير محدد',
        };
    }
}
