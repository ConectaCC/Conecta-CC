<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
final class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'display_name' => $this->display_name,
            'initials' => $this->initials(),
            'role' => $this->role,
            'profile_complete' => $this->isProfileComplete(),
        ];
    }
}
