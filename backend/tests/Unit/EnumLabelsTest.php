<?php

use App\Enums\AuditAction;
use App\Enums\OpportunityType;

it('lista os rótulos dos filtros do painel por valor', function (string $enum) {
    $labels = $enum::labels();

    expect(array_keys($labels))->toBe(array_column($enum::cases(), 'value'));
    foreach ($enum::cases() as $case) {
        expect($labels[$case->value])->toBe($case->label());
    }
})->with([AuditAction::class, OpportunityType::class]);
