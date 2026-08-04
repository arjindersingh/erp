<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<AcademicYear> */
final class AcademicYearFactory extends Factory
{
    public function definition(): array
    {
        $year = fake()->numberBetween(2025, 2035);

        return [
            'uuid' => (string) Str::uuid(), 'tenant_id' => Tenant::factory(), 'code' => "$year-".($year + 1),
            'name' => $year.'–'.substr((string) ($year + 1), -2), 'starts_on' => "$year-04-01",
            'ends_on' => ($year + 1).'-03-31', 'is_current' => false, 'is_default' => false, 'status' => 'draft',
        ];
    }

    public function current(): static
    {
        return $this->state(fn (): array => ['is_current' => true, 'is_default' => true, 'status' => 'active']);
    }

    public function locked(): static
    {
        return $this->state(fn (): array => ['status' => 'locked', 'locked_at' => now()]);
    }
}
