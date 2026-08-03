<?php

namespace App\Core\Organization;

use App\Core\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['tenant_id', 'name', 'slug', 'code', 'description', 'is_active'])]
class InstituteType extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    public function institutes(): HasMany
    {
        return $this->hasMany(Institute::class);
    }
}
