<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Domains\Academics\Enums\AcademicRecordStatus;
use App\Domains\Academics\Enums\EducationLevelCategory;
use App\Domains\Academics\Scopes\SharedOrTenantScope;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\EducationLevelFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

#[UseFactory(EducationLevelFactory::class)]
final class EducationLevel extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::addGlobalScope(new SharedOrTenantScope);
        self::saving(function (self $level): void {
            $level->ownership_key = $level->tenant_id === null ? 'platform' : 'tenant:'.$level->tenant_id;
            if ($level->minimum_age !== null && $level->maximum_age !== null && $level->minimum_age > $level->maximum_age) {
                throw ValidationException::withMessages(['maximum_age' => 'Maximum age must be greater than or equal to minimum age.']);
            }
            if ($level->is_system && $level->tenant_id !== null) {
                throw ValidationException::withMessages(['tenant_id' => 'System education levels must be platform-owned.']);
            }
        });
    }

    protected function casts(): array
    {
        return ['level_category' => EducationLevelCategory::class, 'status' => AcademicRecordStatus::class, 'is_system' => 'boolean'];
    }
}
