<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Domains\Workforce\Enums\DepartmentType;
use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(DepartmentFactory::class)]
final class Department extends WorkforceModel
{
    use HasFactory;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class);
    }

    protected function casts(): array
    {
        return ['department_type' => DepartmentType::class];
    }
}
