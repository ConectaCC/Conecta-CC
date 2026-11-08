<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ChangeUserRole
{
    public function handle(User $actor, User $target, Role $role): void
    {
        // ninguém muda o próprio papel: assim o ADMINISTRADOR em uso nunca se tranca para fora do painel
        abort_if($actor->role !== Role::Administrador || $actor->is($target), 403);

        $from = $target->role;

        if ($from === $role) {
            return;
        }

        DB::transaction(function () use ($actor, $target, $from, $role): void {
            $target->role = $role;
            $target->save();

            app(RecordAuditEvent::class)->handle(AuditAction::RoleChanged, $actor, $target, data: [
                'from' => $from->value,
                'to' => $role->value,
                'via' => 'painel',
            ]);
        });

        // sem e-mail nem nome (decisão 8)
        Log::info('Papel alterado', [
            'actor_id' => $actor->id,
            'target_id' => $target->id,
            'from' => $from->value,
            'to' => $role->value,
        ]);
    }
}
