<?php

namespace App\Core\Tenancy;

use App\Core\Authorization\AccessScope;
use App\Core\Identity\Person;
use App\Core\Identity\Profile;
use App\Core\Identity\UserMembership;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Organization\InstituteType;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[UseFactory(TenantFactory::class)]
#[Fillable(['uuid', 'name', 'legal_name', 'slug', 'code', 'status', 'timezone', 'locale', 'currency', 'branding', 'settings'])]
class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'status' => TenantStatus::Active->value,
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'INR',
    ];

    protected function casts(): array
    {
        return [
            'status' => TenantStatus::class,
            'branding' => 'array',
            'settings' => 'array',
        ];
    }

    protected function slug(): Attribute
    {
        return Attribute::set(fn (?string $value, array $attributes) => $value ?: Str::slug($attributes['name'] ?? ''));
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function campuses(): HasMany
    {
        return $this->hasMany(Campus::class);
    }

    public function instituteTypes(): HasMany
    {
        return $this->hasMany(InstituteType::class);
    }

    public function institutes(): HasMany
    {
        return $this->hasMany(Institute::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function isActive(): bool
    {
        return $this->status->allowsRequests() && ! $this->trashed();
    }

    public function accessScopes(): HasMany
    {
        return $this->hasMany(AccessScope::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function userMemberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_memberships')
            ->withPivot([
                'uuid',
                'person_id',
                'profile_id',
                'access_scope_id',
                'membership_type',
                'is_primary',
                'starts_at',
                'ends_at',
                'status',
            ])
            ->distinct();
    }
}
