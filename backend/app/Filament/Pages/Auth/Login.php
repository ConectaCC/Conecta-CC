<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Support\Str;

final class Login extends BaseLogin
{
    #[Exposed]
    public function authenticate(): ?LoginResponse
    {
        // a mesma normalização do LoginRequest da API
        $email = $this->data['email'] ?? '';
        $this->data['email'] = is_string($email) ? Str::lower(trim($email)) : $email;

        return parent::authenticate();
    }

    public function getTitle(): string
    {
        return 'Entrar';
    }

    public function getHeading(): string
    {
        return 'Painel administrativo';
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()->label('Entrar');
    }

    // o app não tem "Lembrar de mim": a sessão já dura 7 dias sem uso
    #[Unexposed]
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
        ]);
    }
}
