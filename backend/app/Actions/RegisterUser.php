<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\Role;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RegisterUser
{
    public function handle(string $name, string $email, string $password, string $termsVersion): User
    {
        try {
            return DB::transaction(function () use ($name, $email, $password, $termsVersion): User {
                $user = new User(['name' => $name, 'display_name' => $name, 'email' => $email, 'password' => $password]);
                $user->role = Role::Aluno;
                $user->save();

                // RF-04: o aceite só existe se a conta existir (mesma transação)
                $user->termsAcceptances()->create(['version' => $termsVersion, 'accepted_at' => now()]);
                app(RecordAuditEvent::class)->handle(AuditAction::UserRegistered, $user, $user);

                return $user;
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => RegisterRequest::EMAIL_TAKEN]);
        }
    }
}
