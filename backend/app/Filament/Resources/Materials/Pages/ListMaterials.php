<?php

namespace App\Filament\Resources\Materials\Pages;

use App\Filament\Resources\Materials\MaterialResource;
use Filament\Resources\Pages\ListRecords;

final class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    public function getSubheading(): string
    {
        return 'Consulte os materiais enviados e as decisões de Moderação.';
    }
}
