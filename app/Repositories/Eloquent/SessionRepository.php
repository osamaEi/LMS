<?php

namespace App\Repositories\Eloquent;

use App\Models\Session;
use App\Repositories\Contracts\SessionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SessionRepository extends BaseRepository implements SessionRepositoryInterface
{
    public function __construct(Session $model)
    {
        parent::__construct($model);
    }

    public function getBySubject(int $subjectId): Collection
    {
        return $this->model->where('subject_id', $subjectId)
            ->orderBy('session_number', 'asc')
            ->get();
    }

    public function getLiveSessions(): Collection
    {
        return $this->model->where('type', 'live_zoom')
            ->with('subject')
            ->get();
    }

    public function getRecordedSessions(): Collection
    {
        return $this->model->where('type', 'recorded_video')
            ->with('subject')
            ->get();
    }

    public function getUpcomingSessions(int $subjectId = null): Collection
    {
        $query = $this->model->where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->with('subject');

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        return $query->orderBy('scheduled_at', 'asc')->get();
    }

    public function getWithAttendance(int $sessionId): mixed
    {
        return $this->model->with(['attendances.student', 'subject'])
            ->findOrFail($sessionId);
    }

    public function calculateAttendanceRate(int $sessionId): float
    {
        $session = $this->find($sessionId);

        if (!$session) {
            return 0;
        }

        return $session->getAttendanceRate();
    }
}
