<?php

namespace App\Filament\Resources\AuditEvents\Pages;

use App\Filament\Resources\AuditEvents\AuditEventResource;
use Filament\Resources\Pages\ListRecords;

final class ListAuditEvents extends ListRecords
{
    protected static string $resource = AuditEventResource::class;

    public function getSubheading(): string
    {
        return 'Consulte o que foi feito no sistema e por quem.';
    }
}
