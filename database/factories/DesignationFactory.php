<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Tenancy\Tenant;
use App\Domains\Workforce\Models\Designation;
use App\Domains\Workforce\Models\DesignationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Designation> */
final class DesignationFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => Tenant::factory(), 'designation_category_id' => DesignationCategory::factory(), 'code' => strtoupper(fake()->unique()->bothify('DES-###')), 'name' => fake()->jobTitle(), 'sequence' => 10, 'is_teaching_designation' => false, 'is_management_designation' => false, 'status' => 'active'];
    }
}
