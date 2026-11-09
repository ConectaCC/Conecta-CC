<?php

use App\Models\User;

it('manda os cabeçalhos de segurança em toda resposta', function (string $url) {
    $this->get($url)
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
})->with(['/api/v1/terms/current', '/up', '/admin/login']);

it('só manda o HSTS por HTTPS', function () {
    $this->get('https://localhost/up')->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    $this->get('http://localhost/up')->assertHeaderMissing('Strict-Transport-Security');
});

it('a SPA sai com uma política de conteúdo que só aceita scripts do próprio site', function () {
    fakeSpaBuild('<!doctype html><title>ConectaCC</title>');

    $csp = $this->get('/login')->assertOk()->headers->get('Content-Security-Policy');

    expect($csp)->toContain("default-src 'self'")
        ->toContain("script-src 'self'")
        ->toContain("object-src 'none'")
        ->toContain("frame-ancestors 'none'")
        ->toContain("base-uri 'self'")
        ->toContain("form-action 'self'")
        ->toContain("font-src 'self' data:")
        ->not->toContain("'unsafe-eval'")
        ->and(str($csp)->after('script-src')->before(';')->value())->not->toContain('unsafe-inline');
});

it('não responde a pedidos de outra origem (a SPA é da mesma origem)', function () {
    $this->withHeaders(['Origin' => 'https://evil.example'])->getJson('/api/v1/terms/current')
        ->assertHeaderMissing('Access-Control-Allow-Origin');
    $this->withHeaders(['Origin' => 'https://evil.example', 'Access-Control-Request-Method' => 'POST'])->options('/api/v1/login')
        ->assertHeaderMissing('Access-Control-Allow-Origin');
});

it('usa o IP que o nginx já resolveu, sem reler o X-Forwarded-For', function () {
    // em produção o nginx da imagem já põe em REMOTE_ADDR o IP real, confiando no Cloudflare e no Render
    $this->withServerVariables(['REMOTE_ADDR' => '200.1.2.3'])
        ->withHeaders(['X-Forwarded-For' => '6.6.6.6, 173.245.48.1'])
        ->get('/up');

    expect(request()->ip())->toBe('200.1.2.3');
});

it('reconhece o HTTPS informado pelo proxy', function () {
    $this->withHeaders(['X-Forwarded-Proto' => 'https'])->get('http://localhost/up')
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

it('não confia no host vindo do proxy para montar endereços', function () {
    $this->withHeaders(['X-Forwarded-Host' => 'evil.example'])->get('/up');

    expect(request()->getHost())->not->toBe('evil.example');
});

it('limita a API a 120 pedidos por minuto por pessoa', function () {
    $user = User::factory()->create();

    foreach (range(1, 120) as $ignored) {
        $this->actingAs($user)->getJson('/api/v1/disciplines')->assertOk();
    }

    $this->actingAs($user)->getJson('/api/v1/disciplines')->assertTooManyRequests()
        ->assertJsonPath('message', fn (string $message) => str_starts_with($message, 'Muitas tentativas. Tente novamente em '));
    $this->actingAs(User::factory()->create())->getJson('/api/v1/disciplines')->assertOk();
});
