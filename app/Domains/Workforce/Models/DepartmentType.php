<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\DepartmentTypeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(DepartmentTypeFactory::class)]
final class DepartmentType extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    protected function casts(): array
    {
        return ['is_academic' => 'boolean', 'is_system' => 'boolean'];
    }
}
