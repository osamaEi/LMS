<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklySessionResource extends JsonResource
{
    /**
     * Pass the student's attendances keyed by session_id via additional().
     * ->additional(['attendances' => $attendances])
     */
    public function toArray(Request $request): array
    {
        $attendance = $this->additional['attendances'][$this->id] ?? null;

        // Derive session status
        $now = now();
        if ($this->ended_at) {
            $sessionStatus = 'completed';
        } elseif ($this->started_at) {
            $sessionStatus = 'live';
        } elseif ($this->scheduled_at && $this->scheduled_at->isPast()) {
            $sessionStatus = 'missed';
        } else {
            $sessionStatus = 'upcoming';
        }

        // Derive attendance status
        $attendanceStatus = null;
        if ($attendance) {
            if ($attendance->attended) {
                $attendanceStatus = 'present';
            } else {
                // notes suggest an excuse
                $attendanceStatus = $attendance->notes ? 'excused' : 'absent';
            }
        } elseif ($sessionStatus === 'completed' || $sessionStatus === 'missed') {
            $attendanceStatus = 'absent';
        } elseif ($sessionStatus === 'live') {
            $attendanceStatus = 'in_progress';
        }

        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'title_ar'         => $this->title_ar,
            'title_en'         => $this->title_en,
            'session_number'   => $this->session_number,
            'type'             => $this->type,
            'scheduled_at'     => $this->scheduled_at?->toIso8601String(),
            'duration_minutes' => $this->duration_minutes,
            'started_at'       => $this->started_at?->toIso8601String(),
            'ended_at'         => $this->ended_at?->toIso8601String(),
            'status'           => $sessionStatus,
            'zoom_join_url'    => $sessionStatus === 'live' ? $this->zoom_join_url : null,

            'subject' => $this->whenLoaded('subject', fn() => [
                'id'    => $this->subject->id,
                'name'  => $this->subject->name,
                'color' => $this->subject->color,
            ]),

            'attendance' => $attendance ? [
                'status'           => $attendanceStatus,
                'attended'         => $attendance->attended,
                'joined_at'        => $attendance->joined_at?->toIso8601String(),
                'duration_minutes' => $attendance->duration_minutes,
                'watch_percentage' => $attendance->watch_percentage,
                'notes'            => $attendance->notes,
            ] : [
                'status'           => $attendanceStatus,
                'attended'         => false,
                'joined_at'        => null,
                'duration_minutes' => null,
                'watch_percentage' => null,
                'notes'            => null,
            ],
        ];
    }
}
