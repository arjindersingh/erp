<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Domains\Academics\Enums\AcademicRecordStatus;
use App\Domains\Academics\Enums\EducationAuthorityType;
use App\Domains\Academics\Scopes\SharedOrTenantScope;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\EducationAuthorityFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

#[UseFactory(EducationAuthorityFactory::class)]
final class EducationAuthority extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
        self::saving(function (self $authority): void {
            $authority->ownership_key = $authority->tenant_id === null ? 'platform' : 'tenant:'.$authority->tenant_id;
            if ($authority->is_system && $authority->tenant_id !== null) {
                throw ValidationException::withMessages(['tenant_id' => 'System authorities must be platform-owned.']);
            }
        });
    }

    protected function casts(): array
    {
        return ['authority_type' => EducationAuthorityType::class, 'status' => AcademicRecordStatus::class, 'is_system' => 'boolean'];
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(InstituteAuthorityAffiliation::class);
    }
}
