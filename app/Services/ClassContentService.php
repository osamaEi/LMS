<?php

namespace App\Services;

use App\Models\{ProgramClass, Subject, Session, SubjectFile, SessionFile, Homework};
use Illuminate\Database\Eloquent\Builder;

class ClassContentService
{
    public function subjects(ProgramClass $class): Builder
    {
        return Subject::where(function ($q) use ($class) {
            $q->where('class_id', $class->id)->orWhere(function ($q) use ($class) {
                $q->whereNull('class_id')->where(function ($q) use ($class) {
                    $q->whereHas('term', fn ($t) => $t->where('class_id', $class->id))
                        ->orWhereHas('terms', fn ($t) => $t->where('class_id', $class->id));
                });
            });
        });
    }

    public function sessions(ProgramClass $class): Builder
    {
        return Session::where(function ($q) use ($class) {
            $q->where('class_id', $class->id)->orWhere(function ($q) use ($class) {
                $q->whereNull('class_id')->where(function ($q) use ($class) {
                    $q->whereIn('subject_id', $this->subjects($class)->select('subjects.id'))
                        ->orWhere(fn ($q) => $q->whereNull('subject_id')->where('program_id', $class->program_id));
                });
            });
        });
    }

    public function files(ProgramClass $class): Builder
    {
        return SubjectFile::where(function ($q) use ($class) {
            $q->whereIn('subject_id', $this->subjects($class)->select('subjects.id'))
                ->orWhere(fn ($q) => $q->whereNull('subject_id')->where('program_id', $class->program_id));
        });
    }

    public function sessionFiles(ProgramClass $class): Builder
    {
        return SessionFile::whereIn('session_id', $this->sessions($class)->select('class_sessions.id'))
            ->whereIn('type', ['pdf', 'video']);
    }

    public function homeworks(ProgramClass $class): Builder
    {
        return Homework::where(function ($q) use ($class) {
            // Explicit class ownership takes priority over shared subject/program links.
            $q->where('class_id', $class->id)->orWhere(function ($q) use ($class) {
                $q->whereNull('class_id')->where(function ($q) use ($class) {
                    $q->whereIn('session_id', $this->sessions($class)->select('class_sessions.id'))
                        ->orWhere(function ($q) use ($class) {
                            $q->whereNull('session_id')->where(function ($q) use ($class) {
                                $q->whereIn('subject_id', $this->subjects($class)->select('subjects.id'))
                                    ->orWhere(fn ($q) => $q->whereNull('subject_id')->where('program_id', $class->program_id));
                            });
                        });
                });
            });
        });
    }
}
