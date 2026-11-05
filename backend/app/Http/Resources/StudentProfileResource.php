<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// lista fechada: e-mail, nome completo, papel e histórico privado nunca entram aqui (RF-56)
/** @mixin User */
final class StudentProfileResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'display_name' => $this->display_name,
            'initials' => $this->initials(),
            'about' => $this->about,
            /** @var list<string> */
            'interests' => $this->interests ?? [],
            /** @var list<string> */
            'can_help_with' => $this->can_help_with ?? [],
            /** @var list<string> */
            'needs_help_with' => $this->needs_help_with ?? [],
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url,
            'disciplines' => DisciplineResource::collection($this->disciplines),
        ];
    }
}
