<?php

namespace App\Repositories\Eloquent;

use App\Models\Track;
use App\Repositories\Contracts\TrackRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TrackRepository extends BaseRepository implements TrackRepositoryInterface
{
    public function __construct(Track $model)
    {
        parent::__construct($model);
    }

    public function getActiveTracks(): Collection
    {
        return $this->model->where('status', 'active')
            ->with(['program', 'terms'])
            ->get();
    }

    public function getByProgram(int $programId): Collection
    {
        return $this->model->where('program_id', $programId)
            ->with('terms')
            ->get();
    }

    public function getWithTerms(int $trackId): ?Track
    {
        return $this->model->with(['terms.subjects', 'program'])
            ->find($trackId);
    }

    public function getWithStudents(int $trackId): ?Track
    {
        return $this->model->with(['students', 'terms'])
            ->find($trackId);
    }

    public function hasAllTerms(int $trackId): bool
    {
        $track = $this->find($trackId);

        if (!$track) {
            return false;
        }

        return $track->hasAllTerms();
    }

    public function getCurrentActiveTerm(int $trackId): mixed
    {
        $track = $this->find($trackId);

        if (!$track) {
            return null;
        }

        return $track->getCurrentActiveTerm();
    }

    public function getStudentsCount(int $trackId): int
    {
        $track = $this->find($trackId);

        if (!$track) {
            return 0;
        }

        return $track->getTotalStudents();
    }
}
