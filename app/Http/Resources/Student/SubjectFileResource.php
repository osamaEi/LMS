<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectFileResource extends JsonResource
{
    // For session files, pass session context via ->additional(['session' => $session])
    public function toArray(Request $request): array
    {
        $data = [
            'id'    => $this->id,
            'title' => $this->title,
            'url'   => asset('storage/' . $this->file_path),
            'type'  => $this->file_type,
            'size'  => $this->file_size,
        ];

        if (isset($this->additional['session'])) {
            $session = $this->additional['session'];
            $data['session_id']     = $session->id;
            $data['session_number'] = $session->session_number;
        }

        return $data;
    }
}
