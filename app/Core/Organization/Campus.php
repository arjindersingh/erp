<?php

namespace App\Core\Organization;

use App\Core\Authorization\AccessScope;
use App\Core\Tenancy\Tenant;
use Database\Factories\CampusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[UseFactory(CampusFactory::class)]
#[Fillable([
    'tenant_id',
    'company_id',
    'name',
    'slug',
    'code',
    'status',
    'address_line_one',
    'address_line_two',
    'city',
    'state',
    'postal_code',
    'country',
    'settings',
])]
class Campus extends Model
{
    /** @use HasFactory<CampusFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    protected function slug(): Attribute
    {
        return Attribute::set(fn (?string $value, array $attributes) => $value ?: Str::slug($attributes['name'] ?? ''));
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function institutes(): HasMany
    {
        return $this->hasMany(Institute::class);
    }

    public function accessScopes(): HasMany
    {
        return $this->hasMany(AccessScope::class);
    }
}
