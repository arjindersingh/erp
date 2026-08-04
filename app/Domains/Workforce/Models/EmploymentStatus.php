<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\EmploymentStatusFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[UseFactory(EmploymentStatusFactory::class)]
final class EmploymentStatus extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    protected function casts(): array
    {
        return ['is_active_status' => 'boolean', 'is_terminal_status' => 'boolean', 'is_system' => 'boolean'];
    }
}
