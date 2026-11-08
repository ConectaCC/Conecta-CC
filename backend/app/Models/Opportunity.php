<?php

namespace App\Models;

use App\Enums\MaterialStatus;
use App\Enums\OpportunityModality;
use App\Enums\OpportunityType;
use App\Support\Campus;
use App\Support\Conflict;
use Database\Factories\OpportunityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property OpportunityType $type
 * @property string $title
 * @property string $description
 * @property string $url
 * @property OpportunityModality $modality
 * @property Carbon|null $deadline
 * @property MaterialStatus $status
 * @property string|null $rejection_reason
 * @property int|null $decided_by
 * @property Carbon|null $decided_at
 * @property Carbon|null $published_at
 * @property Carbon $created_at
 * @property-read string $code
 * @property-read User $author
 * @property-read User|null $decider
 */
#[Fillable(['type', 'title', 'description', 'url', 'modality', 'deadline'])]
class Opportunity extends Model
{
    /** @use HasFactory<OpportunityFactory> */
    use HasFactory, SoftDeletes;

    // sem "Inscrições até", a oportunidade fica visível por estes dias depois da aprovação (decisoes.md §70)
    public const DEFAULT_VISIBLE_DAYS = 15;

    protected function casts(): array
    {
        return [
            'type' => OpportunityType::class,
            'modality' => OpportunityModality::class,
            'status' => MaterialStatus::class,
            'deadline' => 'date',
            'decided_at' => 'datetime',
            'published_at' => 'datetime',
        ];
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

    /**
     * Aprovadas e dentro do prazo, até o fim do último dia no horário de Brasília.
     *
     * @param  Builder<Opportunity>  $query
     */
    public function scopeVisible(Builder $query): void
    {
        $query->where('opportunities.status', MaterialStatus::Aprovado)
            ->whereRaw(
                "COALESCE(opportunities.deadline, (opportunities.published_at AT TIME ZONE 'UTC' AT TIME ZONE ?)::date + ?::integer) >= ?::date",
                [Campus::TIMEZONE, self::DEFAULT_VISIBLE_DAYS, Campus::today()],
            );
    }

    public function isExpired(): bool
    {
        if ($this->status !== MaterialStatus::Aprovado) {
            return false;
        }

        // a aprovada sempre tem published_at: o ?-> só satisfaz o tipo
        $last = $this->deadline?->toDateString()
            ?? $this->published_at?->setTimezone(Campus::TIMEZONE)->addDays(self::DEFAULT_VISIBLE_DAYS)->toDateString(); // @pest-mutate-ignore: RemoveNullSafeOperator

        return $last !== null && $last < Campus::today();
    }

    // a decisão chegou tarde: o autor cancelou ou outra pessoa decidiu antes
    public function abortDecisionConflict(): never
    {
        if ($this->refresh()->status === MaterialStatus::Cancelado) {
            Conflict::abort('opportunity_cancelled', 'Esta oportunidade foi cancelada por quem enviou.');
        }

        Conflict::abort('already_decided', 'Esta oportunidade já foi analisada.');
    }

    /** @return Attribute<non-falsy-string, never> */
    protected function code(): Attribute
    {
        return Attribute::get(fn (): string => sprintf('OPO-%05d', $this->id));
    }
}
