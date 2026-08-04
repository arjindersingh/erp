<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicNomenclatureSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<AcademicNomenclatureSetting> */
final class AcademicNomenclatureSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(), 'tenant_id' => Tenant::factory(), 'entity_key' => 'class',
            'singular_label' => 'Class', 'plural_label' => 'Classes', 'locale' => 'en', 'status' => 'active',
        ];
    }
}
