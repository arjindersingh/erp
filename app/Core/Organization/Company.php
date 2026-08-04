<?php

namespace App\Core\Organization;

use App\Core\Authorization\AccessScope;
use App\Shared\Support\BelongsToTenant;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[UseFactory(CompanyFactory::class)]
#[Fillable(['tenant_id', 'name', 'slug', 'code', 'type', 'registration_number', 'status', 'settings'])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

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

    public function campuses(): HasMany
    {
        return $this->hasMany(Campus::class);
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
