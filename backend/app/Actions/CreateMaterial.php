<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Http\Requests\StoreMaterialRequest;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateMaterial
{
    /**
     * @param  array{title: string, description: ?string, url: string, anonymous: bool}  $data
     */
    public function handle(User $author, Discipline $discipline, array $data): Material
    {
        $material = new Material($data);
        $material->discipline_id = $discipline->id;
        $material->user_id = $author->id;
        $material->status = MaterialStatus::Pendente;

        try {
            DB::transaction(function () use ($material, $author): void {
                $material->save();
                app(RecordAuditEvent::class)->handle(AuditAction::MaterialSubmitted, $author, material: $material);
            });
        } catch (UniqueConstraintViolationException) {
            // dois envios do mesmo link ao mesmo tempo: o índice parcial barra o segundo
            throw ValidationException::withMessages(['url' => StoreMaterialRequest::DUPLICATE_URL]);
        }

        return $material->setRelation('author', $author);
    }
}
