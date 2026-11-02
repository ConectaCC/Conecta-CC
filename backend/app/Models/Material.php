<?php

namespace App\Models;

use App\Enums\MaterialStatus;
use Database\Factories\MaterialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $discipline_id
 * @property int $user_id
 * @property int|null $revises_id
 * @property string $title
 * @property string|null $description
 * @property string $url
 * @property bool $anonymous
 * @property MaterialStatus $status
 * @property string|null $rejection_reason
 * @property int|null $decided_by
 * @property Carbon|null $decided_at
 * @property Carbon|null $published_at
 * @property Carbon $created_at
 * @property-read string $code
 * @property-read Discipline $discipline
 * @property-read User $author
 */
#[Fillable(['title', 'description', 'url', 'anonymous'])]
class Material extends Model
{
    /** @use HasFactory<MaterialFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'anonymous' => 'boolean',
            'status' => MaterialStatus::class,
            'decided_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Discipline, $this> */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /** @return BelongsTo<Material, $this> */
    public function revisionOf(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'revises_id');
    }

    /** @return Attribute<non-falsy-string, never> */
    protected function code(): Attribute
    {
        return Attribute::get(fn (): string => sprintf('MAT-%05d', $this->id));
    }
}
