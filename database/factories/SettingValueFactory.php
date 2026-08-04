<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\SettingValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SettingValue> */
class SettingValueFactory extends Factory
{
    protected $model = SettingValue::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => null,
            'setting_definition_id' => null,
            'scope_type' => 'platform',
            'scope_id' => null,
            'value_json' => ['value' => $this->faker->word()],
            'effective_from' => now(),
            'effective_until' => null,
            'status' => 'active',
            'created_by' => null,
            'updated_by' => null,
            'approved_by' => null,
            'approved_at' => null,
        ];
    }
}
