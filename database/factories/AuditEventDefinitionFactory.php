<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Audit\AuditEventDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<AuditEventDefinition> */
class AuditEventDefinitionFactory extends Factory
{
    public function definition(): array
    {
        $code = 'test.'.fake()->unique()->slug(2, '.');

        return [
            'uuid' => (string) Str::uuid(), 'event_code' => $code, 'category' => 'data_change',
            'action' => 'updated', 'default_severity' => 'info', 'title_template' => Str::headline(str_replace('.', ' ', $code)),
            'is_security_event' => false, 'is_sensitive' => false, 'is_required' => false, 'status' => 'active',
        ];
    }
}
