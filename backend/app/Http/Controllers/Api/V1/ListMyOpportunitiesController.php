<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Http\Resources\MyOpportunityResource;
use App\Models\Opportunity;
use App\Models\User;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ListMyOpportunitiesController
{
    #[QueryParameter('status', description: 'Situação: all (padrão), pending, approved, rejected ou deleted.', type: 'string')]
    #[QueryParameter('cursor', description: 'Cursor da próxima página (meta.next_cursor).', type: 'string')]
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $query = Opportunity::query()
            ->where('user_id', $user->id)
            ->withTrashed()
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        // os mesmos filtros de Meus conteúdos dos materiais; a encerrada conta como aprovada
        match ($request->query('status')) {
            'pending' => $query->where('status', MaterialStatus::Pendente)->whereNull('deleted_at'),
            'approved' => $query->where('status', MaterialStatus::Aprovado)->whereNull('deleted_at'),
            'rejected' => $query->where('status', MaterialStatus::Rejeitado),
            'deleted' => $query->whereNotNull('deleted_at'),
            default => $query,
        };

        return MyOpportunityResource::collection($query->cursorPaginate(20));
    }
}
