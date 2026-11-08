<?php

namespace App\Console\Commands;

use App\Actions\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class PromoteUser extends Command
{
    protected $signature = 'conectacc:promote {email : E-mail do usuário} {role : ALUNO, MODERADOR ou ADMINISTRADOR}';

    protected $description = 'Altera o papel de um usuário existente';

    public function handle(): int
    {
        $role = Role::tryFrom(Str::upper(trim((string) $this->argument('role'))));

        if ($role === null) {
            $this->error('Papel inválido. Use ALUNO, MODERADOR ou ADMINISTRADOR.');

            return self::FAILURE;
        }

        $user = User::where('email', Str::lower(trim((string) $this->argument('email'))))->first();

        if ($user === null) {
            $this->error('Nenhum usuário encontrado com este e-mail.');

            return self::FAILURE;
        }

        $from = $user->role;

        if ($from !== $role) {
            DB::transaction(function () use ($user, $from, $role): void {
                $user->role = $role;
                $user->save();

                // sem usuário do sistema por trás: a tela mostra "Workflow do GitHub"
                app(RecordAuditEvent::class)->handle(AuditAction::RoleChanged, null, $user, data: [
                    'from' => $from->value,
                    'to' => $role->value,
                    'via' => 'workflow',
                ]);
            });
        }

        // sem o e-mail na saída: o workflow promote-user roda num repositório público
        $this->info("Papel atualizado para {$role->value}.");

        return self::SUCCESS;
    }
}
