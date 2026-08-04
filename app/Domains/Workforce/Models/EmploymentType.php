<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\EmploymentTypeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[UseFactory(EmploymentTypeFactory::class)]
final class EmploymentType extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }
}
