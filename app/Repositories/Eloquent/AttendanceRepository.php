<?php

namespace App\Repositories\Eloquent;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository extends BaseRepository implements AttendanceRepositoryInterface
{
    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    public function getBySession(int $sessionId): Collection
    {
        return $this->model->where('session_id', $sessionId)
            ->with('student')
            ->get();
    }

    public function getByStudent(int $studentId): Collection
    {
        return $this->model->where('student_id', $studentId)
            ->with(['session.subject'])
            ->get();
    }

    public function markAttended(int $studentId, int $sessionId, array $data = []): mixed
    {
        $attendance = $this->getOrCreate($studentId, $sessionId);

        $attendance->update(array_merge($data, [
            'attended' => true,
        ]));

        return $attendance;
    }

    public function updateWatchProgress(int $studentId, int $sessionId, float $percentage): bool
    {
        $attendance = $this->getOrCreate($studentId, $sessionId);

        $attendance->updateWatchProgress($percentage);

        return true;
    }

    public function hasAttended(int $studentId, int $sessionId): bool
    {
        return $this->model->where('student_id', $studentId)
            ->where('session_id', $sessionId)
            ->where('attended', true)
            ->exists();
    }

    public function calculateStudentAttendanceRate(int $studentId, int $subjectId = null): float
    {
        $query = $this->model->where('student_id', $studentId);

        if ($subjectId) {
            $query->whereHas('session', function ($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            });
        }

        $totalSessions = $query->count();

        if ($totalSessions === 0) {
            return 0;
        }

        $attendedSessions = $query->where('attended', true)->count();

        return round(($attendedSessions / $totalSessions) * 100, 2);
    }

    public function getOrCreate(int $studentId, int $sessionId): mixed
    {
        return $this->model->firstOrCreate(
            [
                'student_id' => $studentId,
                'session_id' => $sessionId,
            ],
            [
                'attended' => false,
                'watch_percentage' => 0,
                'video_completed' => false,
            ]
        );
    }
}
