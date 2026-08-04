<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\Person;
use App\Domains\Students\Models\GuardianOccupation;
use App\Domains\Students\Models\GuardianProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GuardianProfile> */
final class GuardianProfileFactory extends Factory
{
    public function definition(): array
    {
        return ['person_id' => Person::factory(), 'tenant_id' => fn (array $a) => Person::withoutGlobalScopes()->findOrFail($a['person_id'])->tenant_id, 'guardian_number' => strtoupper(fake()->unique()->bothify('GRD-####')), 'occupation_id' => GuardianOccupation::factory(), 'portal_access_allowed' => false, 'communication_allowed' => true, 'financial_contact_allowed' => false, 'status' => 'active'];
    }
}
