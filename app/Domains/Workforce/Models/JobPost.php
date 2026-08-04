<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use Database\Factories\JobPostFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(JobPostFactory::class)]
final class JobPost extends WorkforceModel
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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function defaultEmploymentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class, 'default_employment_type_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmploymentAssignment::class);
    }

    protected function casts(): array
    {
        return ['is_teaching_post' => 'boolean'];
    }
}
