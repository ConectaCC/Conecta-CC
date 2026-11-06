<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('ConectaCC')
            ->brandLogo(new HtmlString('<span style="font-size:1.5rem;font-weight:700;color:#071f43">Conecta<span style="color:#2259b9">CC</span></span>'))
            ->colors([
                // o Color::hex deixa o tom 600 claro demais para texto branco, e o botão perderia a cor da marca
                'primary' => array_replace(Color::hex('#2259b9'), [
                    500 => 'oklch(0.545 0.163 260.71)',
                    600 => 'oklch(0.485 0.163 260.71)',
                    700 => 'oklch(0.425 0.163 260.71)',
                ]),
                'gray' => Color::Slate,
                'success' => Color::hex('#1e6b3a'),
                'warning' => Color::hex('#b7791f'),
                'danger' => Color::hex('#b42318'),
                'change' => Color::hex('#9c3a00'),
            ])
            ->darkMode(false)
            ->globalSearch(false)
            ->userMenuItems([
                Action::make('backToApp')
                    ->label('Voltar ao ConectaCC')
                    ->icon(Heroicon::OutlinedArrowLeft)
                    ->url('/home'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
