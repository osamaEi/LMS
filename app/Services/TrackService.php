<?php

namespace App\Services;

use App\Repositories\Contracts\TrackRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TrackService
{
    public function __construct(
        protected TrackRepositoryInterface $trackRepository
    ) {
    }

    public function getAllTracks(array $filters = [])
    {
        if (isset($filters['program_id'])) {
            return $this->trackRepository->getByProgram($filters['program_id']);
        }

        if (isset($filters['status']) && $filters['status'] === 'active') {
            return $this->trackRepository->getActiveTracks();
        }

        return $this->trackRepository->all(['*'], ['program']);
    }

    public function getTrack(int $id, bool $withTerms = false, bool $withStudents = false)
    {
        if ($withTerms) {
            return $this->trackRepository->getWithTerms($id);
        }

        if ($withStudents) {
            return $this->trackRepository->getWithStudents($id);
        }

        return $this->trackRepository->findOrFail($id, ['*'], ['program']);
    }

    public function createTrack(array $data)
    {
        DB::beginTransaction();

        try {
            $track = $this->trackRepository->create($data);

            // إنشاء الـ 10 أرباع تلقائياً
            if (isset($data['auto_create_terms']) && $data['auto_create_terms']) {
                $this->createTermsForTrack($track->id, $data['term_duration_months'] ?? 3);
            }

            DB::commit();
            return $track;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateTrack(int $id, array $data)
    {
        return $this->trackRepository->update($id, $data);
    }

    public function deleteTrack(int $id)
    {
        return $this->trackRepository->delete($id);
    }

    public function getTrackWithTerms(int $id)
    {
        return $this->trackRepository->getWithTerms($id);
    }

    public function getCurrentActiveTerm(int $trackId)
    {
        return $this->trackRepository->getCurrentActiveTerm($trackId);
    }

    public function getStudentsCount(int $trackId): int
    {
        return $this->trackRepository->getStudentsCount($trackId);
    }

    /**
     * إنشاء 10 أرباع تلقائياً للمسار
     */
    protected function createTermsForTrack(int $trackId, int $durationMonths = 3)
    {
        $track = $this->trackRepository->find($trackId);

        if (!$track) {
            throw new \Exception("Track not found");
        }

        $startDate = now();

        for ($i = 1; $i <= 10; $i++) {
            $termStartDate = $startDate->copy()->addMonths(($i - 1) * $durationMonths);
            $termEndDate = $termStartDate->copy()->addMonths($durationMonths)->subDay();

            $registrationStart = $termStartDate->copy()->subWeeks(2);
            $registrationEnd = $termStartDate->copy()->subDays(3);

            \App\Models\Term::create([
                'program_id' => $track->program_id,
                'track_id' => $track->id,
                'term_number' => $i,
                'name' => "الربع {$i}",
                'start_date' => $termStartDate,
                'end_date' => $termEndDate,
                'registration_start_date' => $registrationStart,
                'registration_end_date' => $registrationEnd,
                'status' => $i === 1 ? 'active' : 'upcoming',
            ]);
        }
    }

    /**
     * تعيين الطالب إلى مسار
     */
    public function assignStudentToTrack(int $studentId, int $trackId, int $termNumber = 1)
    {
        $track = $this->trackRepository->find($trackId);

        if (!$track) {
            throw new \Exception("Track not found");
        }

        $student = \App\Models\User::findOrFail($studentId);

        return $student->update([
            'track_id' => $trackId,
            'current_term_number' => $termNumber,
        ]);
    }

    /**
     * ترقية الطالب للربع التالي
     */
    public function promoteStudentToNextTerm(int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);

        if (!$student->track_id || !$student->current_term_number) {
            throw new \Exception("Student is not assigned to any track");
        }

        if ($student->current_term_number >= 10) {
            throw new \Exception("Student has completed all terms");
        }

        return $student->update([
            'current_term_number' => $student->current_term_number + 1,
        ]);
    }
}
