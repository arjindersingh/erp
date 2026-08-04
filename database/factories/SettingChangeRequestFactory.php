<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\SettingChangeRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SettingChangeRequest> */
class SettingChangeRequestFactory extends Factory
{
    protected $model = SettingChangeRequest::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => null,
            'setting_value_id' => null,
            'requested_by' => null,
            'approved_by' => null,
            'status' => 'pending',
            'request_reason' => $this->faker->sentence(),
            'review_notes' => null,
            'metadata_json' => [],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
