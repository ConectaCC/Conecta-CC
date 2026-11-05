<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property Role $role
 * @property-read string $public_code
 * @property string|null $about
 * @property list<string> $interests
 * @property list<string> $can_help_with
 * @property list<string> $needs_help_with
 * @property string|null $github_url
 * @property string|null $linkedin_url
 */
// role fica fora do Fillable: papel só muda por código explícito
#[Fillable(['name', 'display_name', 'email', 'password', 'about', 'interests', 'can_help_with', 'needs_help_with', 'github_url', 'linkedin_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'interests' => 'array',
            'can_help_with' => 'array',
            'needs_help_with' => 'array',
        ];
    }

    /** @return HasMany<TermsAcceptance, $this> */
    public function termsAcceptances(): HasMany
    {
        return $this->hasMany(TermsAcceptance::class);
    }

    /** @return HasMany<Material, $this> */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /** @return BelongsToMany<Discipline, $this> */
    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class)->orderBy('semester')->orderBy('name');
    }

    // decisoes.md §8: Nome de exibição + pelo menos uma disciplina atual
    public function isProfileComplete(): bool
    {
        return trim((string) $this->display_name) !== '' && $this->disciplines()->exists();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/u', trim((string) $this->display_name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $last = count($parts) > 1 ? mb_substr((string) end($parts), 0, 1) : '';

        return mb_strtoupper($first.$last);
    }
}
