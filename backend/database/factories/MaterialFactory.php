<?php

namespace Database\Factories;

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            // o catálogo vem da migration de dados, então sempre há disciplinas
            'discipline_id' => fn () => Discipline::inRandomOrder()->value('id'),
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'url' => 'https://example.com/'.fake()->unique()->slug(),
            'anonymous' => false,
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

    public function anonymous(): static
    {
        return $this->state(fn () => ['anonymous' => true]);
    }
}
