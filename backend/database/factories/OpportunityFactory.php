<?php

namespace Database\Factories;

use App\Enums\MaterialStatus;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opportunity>
 */
class OpportunityFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => 'ESTAGIO',
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'url' => 'https://example.com/'.fake()->unique()->slug(),
            'modality' => 'REMOTO',
            'deadline' => null,
            'status' => MaterialStatus::Pendente->value,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => MaterialStatus::Aprovado->value, 'published_at' => now(), 'decided_at' => now()]);
    }

    public function rejected(string $reason): static
    {
        return $this->state(fn () => ['status' => MaterialStatus::Rejeitado->value, 'rejection_reason' => $reason, 'decided_at' => now()]);
    }
}
