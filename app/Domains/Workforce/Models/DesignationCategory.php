<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\DesignationCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(DesignationCategoryFactory::class)]
final class DesignationCategory extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class);
    }

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }
}
