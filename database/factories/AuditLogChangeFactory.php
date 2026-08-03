<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Audit\AuditLog;
use App\Core\Audit\AuditLogChange;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AuditLogChange> */
class AuditLogChangeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'audit_log_id' => AuditLog::factory(), 'field_name' => fake()->unique()->word(),
            'old_value_text' => 'old', 'new_value_text' => 'new', 'data_type' => 'string',
            'is_sensitive' => false, 'is_masked' => false,
        ];
    }
}
