<?php

namespace App\Filament\Resources\Opportunities\Pages;

use App\Filament\Resources\Opportunities\OpportunityResource;
use Filament\Resources\Pages\ListRecords;

final class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;

    public function getSubheading(): string
    {
        return 'Consulte as oportunidades enviadas, quem enviou e as decisões de Moderação.';
    }
}
