<?php

declare(strict_types=1);

namespace App\Domains\Students\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\StudentStatusFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(StudentStatusFactory::class)]
final class StudentStatus extends StudentDomainModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    public function students(): HasMany
    {
        return $this->hasMany(StudentProfile::class);
    }

    protected function casts(): array
    {
        return ['is_active_status' => 'boolean', 'is_terminal_status' => 'boolean', 'allows_enrolment' => 'boolean', 'allows_portal_access' => 'boolean', 'allows_financial_activity' => 'boolean', 'is_system' => 'boolean'];
    }
}
