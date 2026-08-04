<?php

declare(strict_types=1);

namespace App\Domains\Students\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\GuardianOccupationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(GuardianOccupationFactory::class)]
final class GuardianOccupation extends StudentDomainModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(GuardianProfile::class, 'occupation_id');
    }

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }
}
