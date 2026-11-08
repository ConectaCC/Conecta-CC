<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Support\InitialsAvatarProvider;
use App\Models\User;
use App\Support\Campus;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentTimezone;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    // o banco guarda em UTC; o painel mostra e filtra no horário do câmpus, como o app faz no navegador
    public const TIMEZONE = Campus::TIMEZONE;

    public function boot(): void
    {
        FilamentTimezone::set(self::TIMEZONE);
    }

    private static function displayName(): string
    {
        $user = Filament::auth()->user();

        // o painel só abre com sessão de User: o '' só satisfaz o tipo
        return $user instanceof User ? $user->display_name : ''; // @pest-mutate-ignore: InstanceOfToTrue,EmptyStringToNotEmpty
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('ConectaCC')
            ->brandLogo(new HtmlString('<span class="conectacc-brand">Conecta<span>CC</span></span>'))
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            ->sidebarWidth('275px')
            ->maxContentWidth(Width::Full)
            ->breadcrumbs(false)
            ->renderHook(PanelsRenderHook::STYLES_AFTER, fn (): HtmlString => new HtmlString(
                '<link rel="stylesheet" href="'.asset('css/conectacc-admin.css').'?v='.filemtime(public_path('css/conectacc-admin.css')).'">'
            ))
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn (): HtmlString => new HtmlString(
                '<span class="conectacc-user-name">'.e(self::displayName()).'</span>'
            ))
            ->renderHook(PanelsRenderHook::SIDEBAR_FOOTER, fn () => view('filament.sidebar-footer'))
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
