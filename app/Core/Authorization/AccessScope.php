<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Identity\IdentityStatus;
use App\Core\Identity\UserMembership;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\AccessScopeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property ScopeType $scope_type
 * @property IdentityStatus $status
 * @property int $tenant_id
 * @property int|null $parent_scope_id
 * @property int|null $company_id
 * @property int|null $campus_id
 * @property int|null $institute_id
 * @property string $uuid
 * @property string $path
 */
#[UseFactory(AccessScopeFactory::class)]
#[Fillable([
    'uuid',
    'tenant_id',
    'parent_scope_id',
    'scope_type',
    'company_id',
    'campus_id',
    'institute_id',
    'name',
    'code',
    'status',
    'metadata',
])]
class AccessScope extends Model
{
    /** @use HasFactory<AccessScopeFactory> */
    use BelongsToTenant, HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'scope_type' => ScopeType::Tenant->value,
        'level' => 0,
        'status' => IdentityStatus::Active->value,
    ];

    protected static function booted(): void
    {
        static::saving(
            fn (AccessScope $scope) => app(ScopeHierarchyValidator::class)->prepareAndValidate($scope)
        );
    }

    protected function casts(): array
    {
        return [
            'scope_type' => ScopeType::class,
            'company_id' => 'integer',
            'campus_id' => 'integer',
            'institute_id' => 'integer',
            'level' => 'integer',
            'status' => IdentityStatus::class,
            'metadata' => 'array',
        ];
    }

    /** @return BelongsTo<AccessScope, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_scope_id');
    }

    /** @return HasMany<AccessScope, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_scope_id');
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** @return BelongsTo<Campus, $this> */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    /** @return BelongsTo<Institute, $this> */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    /** @return HasMany<UserMembership, $this> */
    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    /** @return HasMany<RoleAssignment, $this> */
    public function roleAssignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }

    /**
     * @param  Builder<AccessScope>  $query
     * @return Builder<AccessScope>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', IdentityStatus::Active->value);
    }

    /**
     * @param  Builder<AccessScope>  $query
     * @return Builder<AccessScope>
     */
    public function scopeOfType(Builder $query, ScopeType $type): Builder
    {
        return $query->where('scope_type', $type->value);
    }

    public function isActive(): bool
    {
        return $this->status === IdentityStatus::Active && ! $this->trashed();
    }

    public function canAccessTenant(Tenant|int $tenant): bool
    {
        $tenantId = $tenant instanceof Tenant ? (int) $tenant->getKey() : $tenant;

        return $this->scope_type === ScopeType::Tenant && $this->tenant_id === $tenantId;
    }

    public function canAccessCompany(Company $company): bool
    {
        if ($this->tenant_id !== (int) $company->tenant_id) {
            return false;
        }

        return $this->scope_type === ScopeType::Tenant
            || ($this->scope_type === ScopeType::Company && $this->company_id === (int) $company->getKey());
    }

    public function canAccessCampus(Campus $campus): bool
    {
        if ($this->tenant_id !== (int) $campus->tenant_id) {
            return false;
        }

        return match ($this->scope_type) {
            ScopeType::Tenant => true,
            ScopeType::Company => $this->company_id === (int) $campus->company_id,
            ScopeType::Campus => $this->campus_id === (int) $campus->getKey(),
            ScopeType::Institute => false,
        };
    }

    public function canAccessInstitute(Institute $institute): bool
    {
        if ($this->tenant_id !== (int) $institute->tenant_id) {
            return false;
        }

        return match ($this->scope_type) {
            ScopeType::Tenant => true,
            ScopeType::Company => $this->company_id === (int) $institute->company_id,
            ScopeType::Campus => $this->campus_id === (int) $institute->campus_id,
            ScopeType::Institute => $this->institute_id === (int) $institute->getKey(),
        };
    }

    public function containsScope(AccessScope $scope): bool
    {
        if ($this->tenant_id !== $scope->tenant_id) {
            return false;
        }

        return $scope->path === $this->path
            || str_starts_with($scope->path, rtrim($this->path, '/').'/');
    }

    public function isAncestorOf(AccessScope $scope): bool
    {
        return ! $this->is($scope) && $this->containsScope($scope);
    }

    public function isDescendantOf(AccessScope $scope): bool
    {
        return $scope->isAncestorOf($this);
    }
}
