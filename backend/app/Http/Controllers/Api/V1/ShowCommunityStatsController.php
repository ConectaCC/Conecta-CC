<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\Opportunity;
use App\Models\User;

final class ShowCommunityStatsController
{
    /** @return array{materials: int, opportunities: int, students: int} */
    public function __invoke(): array
    {
        return [
            // a mesma regra do mural: originais aprovados e não excluídos
            'materials' => Material::where('status', MaterialStatus::Aprovado)->whereNull('revises_id')->count(),
            'opportunities' => Opportunity::query()->visible()->count(),
            'students' => User::count(),
        ];
    }
}
