<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Audit\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<AuditLog> */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(), 'actor_type' => 'system', 'category' => 'system',
            'action' => 'job_completed', 'severity' => 'info', 'outcome' => 'success',
            'event_code' => 'test.system.event', 'event_title' => 'Test system event',
            'source' => 'system', 'occurred_at' => now(), 'recorded_at' => now(),
        ];
    }
}
