<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

// TEST_NOW congela o relógio de toda a suíte. O CI roda uma vez às 00:30 UTC, quando em Brasília ainda é o dia anterior,
// para pegar teste que monta data com now() em UTC enquanto a regra conta o dia no fuso do câmpus (Campus::TIMEZONE)
if (getenv('TEST_NOW')) {
    pest()->beforeEach(fn () => $this->travelTo(getenv('TEST_NOW')))->in('Feature');
}
