<?php

namespace App\Models;

use App\Enums\AuditAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property AuditAction $action
 * @property int|null $actor_id
 * @property int|null $user_id
 * @property int|null $material_id
 * @property array<string, mixed> $data
 * @property Carbon $created_at
 * @property-read User|null $actor
 * @property-read User|null $user
 * @property-read Material|null $material
 */
class AuditEvent extends Model
{
    // o evento não muda depois de gravado (trigger no banco)
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'data' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Material, $this> */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class)->withTrashed();
    }
}
