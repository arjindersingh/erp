<?php

declare(strict_types=1);

namespace App\Domains\Students\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\GuardianRelationshipTypeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(GuardianRelationshipTypeFactory::class)]
final class GuardianRelationshipType extends StudentDomainModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(StudentGuardianRelationship::class);
    }

    protected function casts(): array
    {
        return ['is_parent_relationship' => 'boolean', 'is_legal_relationship' => 'boolean', 'is_system' => 'boolean'];
    }
}
