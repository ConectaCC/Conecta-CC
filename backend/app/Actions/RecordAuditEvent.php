<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Models\AuditEvent;
use App\Models\Material;
use App\Models\User;

// chamada dentro da transação da ação: se a ação não valer, o evento também não existe
final class RecordAuditEvent
{
    /** @param array<string, mixed> $data só IDs, papéis e textos do material; nunca e-mail, senha, token ou IP */
    public function handle(AuditAction $action, ?User $actor, ?User $user = null, ?Material $material = null, array $data = []): void
    {
        if ($material !== null) {
            // o título fica no evento porque a aprovação de uma alteração sobrescreve o original
            $data = ['title' => $material->title, 'anonymous' => $material->anonymous, 'revision' => $material->revises_id !== null, ...$data];
        }

        AuditEvent::create([
            'action' => $action,
            'actor_id' => $actor?->id,
            'user_id' => $user?->id,
            'material_id' => $material?->id,
            'data' => $data,
        ]);
    }
}
