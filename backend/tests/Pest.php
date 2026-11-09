<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\ParallelTesting;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

// TEST_NOW congela o relógio de toda a suíte. O CI roda uma vez às 00:30 UTC, quando em Brasília ainda é o dia anterior,
// para pegar teste que monta data com now() em UTC enquanto a regra conta o dia no fuso do câmpus (Campus::TIMEZONE)
if (getenv('TEST_NOW')) {
    pest()->beforeEach(fn () => $this->travelTo(getenv('TEST_NOW')))->in('Feature');
}

// a SPA compilada fica em public/spa; cada processo paralelo usa a própria pasta pública, para um teste não apagar a do outro
function fakeSpaBuild(string $html = '<div id="root"></div>'): void
{
    $public = storage_path('framework/testing/public-'.(ParallelTesting::token() ?: 0));
    File::ensureDirectoryExists($public.'/spa');
    // o resto da pasta pública (CSS do painel, por exemplo) continua sendo o real
    foreach (File::glob(public_path('*')) as $entry) {
        $link = $public.'/'.basename($entry);
        if (basename($entry) !== 'spa' && ! is_link($link)) {
            File::link($entry, $link);
        }
    }
    File::put($public.'/spa/index.html', $html);
    app()->usePublicPath($public);
}
