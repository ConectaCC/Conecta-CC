<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Http\Resources\MyMaterialResource;
use App\Models\Material;
use App\Models\User;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ListMyMaterialsController
{
    #[QueryParameter('status', description: 'Situação: all (padrão), pending, approved, rejected ou deleted.', type: 'string')]
    #[QueryParameter('cursor', description: 'Cursor da próxima página (meta.next_cursor).', type: 'string')]
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        // a partir de Material::query() para o Scramble documentar a resposta paginada
        $query = Material::query()
            ->where('user_id', $user->id)
            ->withTrashed()
            ->whereNull('revises_id')
            ->with(['discipline', 'revisions'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        switch ($request->query('status')) {
            case 'pending':
                $query->where('status', MaterialStatus::Pendente)->whereNull('deleted_at');
                break;
            case 'approved':
                $query->where('status', MaterialStatus::Aprovado)->whereNull('deleted_at');
                break;
            case 'rejected':
                $query->where('status', MaterialStatus::Rejeitado);
                break;
            case 'deleted':
                $query->whereNotNull('deleted_at');
                break;
        }

        return MyMaterialResource::collection($query->cursorPaginate(20));
    }
}
