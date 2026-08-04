<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\SettingValueHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SettingValueHistory> */
class SettingValueHistoryFactory extends Factory
{
    protected $model = SettingValueHistory::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'setting_value_id' => null,
            'old_value_json' => ['value' => $this->faker->word()],
            'new_value_json' => ['value' => $this->faker->word()],
            'changed_fields_json' => [],
            'change_source' => 'system',
            'change_reason' => null,
            'changed_by' => null,
        ];
    }
}
