<?php

declare(strict_types=1);

namespace App\Core\Identity;

use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property IdentityStatus $status
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 */
#[UseFactory(ProfileFactory::class)]
#[Fillable([
    'tenant_id',
    'person_id',
    'uuid',
    'type',
    'display_name',
    'reference_number',
    'status',
    'is_primary',
    'starts_at',
    'ends_at',
    'metadata',
])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use BelongsToTenant, HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'status' => IdentityStatus::Active->value,
        'is_primary' => false,
    ];

    protected function casts(): array
    {
        return [
            'type' => ProfileType::class,
            'status' => IdentityStatus::class,
            'is_primary' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /** @return BelongsTo<Person, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /** @return HasMany<UserMembership, $this> */
    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    public function isActiveAt(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status === IdentityStatus::Active
            && ! $this->trashed()
            && ($this->starts_at === null || $this->starts_at->lessThanOrEqualTo($at))
            && ($this->ends_at === null || $this->ends_at->greaterThan($at));
    }
}
