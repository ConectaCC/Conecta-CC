<?php

namespace App\Enums;

enum AuditAction: string
{
    case UserRegistered = 'user_registered';
    case PasswordChanged = 'password_changed';
    case RoleChanged = 'role_changed';
    case MaterialSubmitted = 'material_submitted';
    case RevisionSubmitted = 'revision_submitted';
    case SubmissionCancelled = 'submission_cancelled';
    case MaterialDeleted = 'material_deleted';
    case MaterialApproved = 'material_approved';
    case MaterialRejected = 'material_rejected';

    public function label(): string
    {
        return match ($this) {
            self::UserRegistered => 'Cadastro',
            self::PasswordChanged => 'Senha alterada',
            self::RoleChanged => 'Papel alterado',
            self::MaterialSubmitted => 'Material enviado',
            self::RevisionSubmitted => 'Alteração enviada',
            self::SubmissionCancelled => 'Envio cancelado',
            self::MaterialDeleted => 'Material excluído',
            self::MaterialApproved => 'Aprovado',
            self::MaterialRejected => 'Rejeitado',
        };
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $action) => [$action->value => $action->label()])->all();
    }
}
