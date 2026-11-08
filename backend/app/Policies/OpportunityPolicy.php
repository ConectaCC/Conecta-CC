<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Opportunity;
use App\Models\User;

final class OpportunityPolicy
{
    // MODERADOR não decide sobre o próprio envio; ADMINISTRADOR pode (decisoes.md §51.2)
    public function moderate(User $user, Opportunity $opportunity): bool
    {
        return $user->role->canModerate()
            && ($user->role === Role::Administrador || $opportunity->user_id !== $user->id);
    }

    // cancelar e excluir: só quem enviou
    public function manage(User $user, Opportunity $opportunity): bool
    {
        return $opportunity->user_id === $user->id;
    }
}
