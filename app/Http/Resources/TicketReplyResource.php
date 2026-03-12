<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'message'        => $this->message,
            'attachment_url' => $this->attachment
                ? asset('storage/' . $this->attachment)
                : null,
            'is_from_staff'  => $this->isFromStaff(),
            'is_from_me'     => $this->whenLoaded('user',
                fn() => $this->user_id === $request->user()?->id
            ),
            'user'           => $this->whenLoaded('user', fn() => [
                'id'            => $this->user->id,
                'name'          => $this->user->name,
                'role'          => $this->user->role,
                'profile_photo' => $this->user->profile_photo
                    ? asset('storage/' . $this->user->profile_photo)
                    : null,
            ]),
            'created_at'     => $this->created_at->toIso8601String(),
        ];
    }
}
