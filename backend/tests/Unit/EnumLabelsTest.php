<?php

use App\Enums\AuditAction;
use App\Enums\OpportunityModality;
use App\Enums\OpportunityType;

it('lista os rótulos dos filtros do painel por valor', function (string $enum) {
    $labels = $enum::labels();

    expect(array_keys($labels))->toBe(array_column($enum::cases(), 'value'));
    foreach ($enum::cases() as $case) {
        expect($labels[$case->value])->toBe($case->label());
    }
})->with([AuditAction::class, OpportunityType::class]);

it('dá o rótulo de cada modalidade', function (OpportunityModality $modality, string $label) {
    expect($modality->label())->toBe($label);
})->with([
    [OpportunityModality::Remoto, 'Remoto'],
    [OpportunityModality::Hibrido, 'Híbrido'],
    [OpportunityModality::Presencial, 'Presencial'],
]);
