<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use Database\Factories\DesignationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(DesignationFactory::class)]
final class Designation extends WorkforceModel
{
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(DesignationCategory::class, 'designation_category_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmploymentAssignment::class);
    }

    protected function casts(): array
    {
        return ['is_teaching_designation' => 'boolean', 'is_management_designation' => 'boolean'];
    }
}
