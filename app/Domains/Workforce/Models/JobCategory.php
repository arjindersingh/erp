<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Domains\Academics\Scopes\SharedOrTenantScope;
use Database\Factories\JobCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(JobCategoryFactory::class)]
final class JobCategory extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class);
    }

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }
}
