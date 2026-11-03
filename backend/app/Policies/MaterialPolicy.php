<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Material;
use App\Models\User;

final class MaterialPolicy
{
    // MODERADOR não decide sobre o próprio envio; ADMINISTRADOR pode (decisoes.md §51.2)
    public function moderate(User $user, Material $material): bool
    {
        return $user->role->canModerate()
            && ($user->role === Role::Administrador || $material->user_id !== $user->id);
    }

    // cancelar, editar e excluir: só o autor, e só sobre o original (nunca sobre uma alteração)
    public function manage(User $user, Material $material): bool
    {
        return $material->user_id === $user->id && $material->revises_id === null;
    }
}
