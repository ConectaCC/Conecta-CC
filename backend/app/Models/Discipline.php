<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property int $semester
 * @property int $workload_hours
 * @property string|null $syllabus_url
 * @property-read string $slug
 */
class Discipline extends Model
{
    protected function casts(): array
    {
        return [
            'semester' => 'integer',
            'workload_hours' => 'integer',
        ];
    }

    // shortcut: calculado do nome, sem coluna; criar coluna se o backend precisar buscar por slug (etapa 2)
    /** @return Attribute<string, never> */
    protected function slug(): Attribute
    {
        return Attribute::get(fn (): string => Str::slug($this->name));
    }

    /** @return HasMany<Material, $this> */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
