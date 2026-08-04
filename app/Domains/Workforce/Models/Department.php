<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

#[UseFactory(DepartmentFactory::class)]
final class Department extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::saving(function (self $department): void {
            if (! $department->parent_id) {
                return;
            }
            $parent = self::withoutGlobalScopes()->find($department->parent_id);
            $sameBoundary = $parent && (int) $parent->tenant_id === (int) $department->tenant_id
                && $parent->company_id === $department->company_id
                && $parent->campus_id === $department->campus_id
                && $parent->institute_id === $department->institute_id;
            if (! $sameBoundary) {
                throw ValidationException::withMessages(['parent_id' => 'Parent department must share the same organisational boundary.']);
            }
        });
    }

    public function departmentType(): BelongsTo
    {
        return $this->belongsTo(DepartmentType::class);
    }

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
}
