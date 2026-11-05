<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProgramClass extends Model
{
    use SoftDeletes;

    protected $table = 'program_classes';

    protected $fillable = [
        'program_id',
        'name',
        'teacher_id',
        'supervisor_name',
        'start_date',
        'end_date',
        'status',
        'max_students',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'max_students' => 'integer',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_programs', 'class_id', 'student_id');
    }

    public function terms()
    {
        return $this->hasMany(Term::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'class_id');
    }

    /**
     * IDs (from $classIds) of classes that are over: marked completed, or whose
     * effective end — the class end date, else its latest term end, else its
     * last scheduled session — is before today.
     */
    public static function pastIds($classIds)
    {
        $today = now()->startOfDay();

        return static::whereIn('id', $classIds)
            ->withMax('terms', 'end_date')
            ->withMax('sessions', 'scheduled_at')
            ->get()
            ->filter(function (self $class) use ($today) {
                if ($class->status === 'completed') {
                    return true;
                }
                $end = $class->end_date ?? $class->terms_max_end_date ?? $class->sessions_max_scheduled_at;

                return $end && \Carbon\Carbon::parse($end)->lt($today);
            })
            ->pluck('id');
    }

    public function getStudentsCountAttribute(): int
    {
        return DB::table('student_programs')
            ->where('class_id', $this->id)
            ->pluck('student_id')
            ->merge(User::where('class_id', $this->id)->pluck('id'))
            ->merge(DB::table('student_class_memberships')->where('class_id', $this->id)->pluck('student_id'))
            ->unique()
            ->count();
    }
}
