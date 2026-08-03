<?php

namespace App\Core\Organization;

use App\Core\Authorization\AccessScope;
use App\Core\Tenancy\Tenant;
use Database\Factories\InstituteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[UseFactory(InstituteFactory::class)]
#[Fillable([
    'tenant_id',
    'company_id',
    'campus_id',
    'institute_type_id',
    'name',
    'slug',
    'code',
    'affiliation_number',
    'status',
    'settings',
])]
class Institute extends Model
{
    /** @use HasFactory<InstituteFactory> */
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

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function instituteType(): BelongsTo
    {
        return $this->belongsTo(InstituteType::class);
    }

    public function accessScopes(): HasMany
    {
        return $this->hasMany(AccessScope::class);
    }
}
