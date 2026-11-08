<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ChangePassword
{
    public function handle(User $user, string $password, string $currentSessionId): void
    {
        DB::transaction(function () use ($user, $password, $currentSessionId): void {
            $user->update(['password' => $password]);

            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->delete();

            app(RecordAuditEvent::class)->handle(AuditAction::PasswordChanged, $user, $user);
        });
    }
}
