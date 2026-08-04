<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Domains\Academics\Enums\AcademicEntityKey;
use App\Domains\Academics\Enums\AcademicRecordStatus;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\AcademicNomenclatureSettingFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(AcademicNomenclatureSettingFactory::class)]
final class AcademicNomenclatureSetting extends Model
{
    use BelongsToTenant, HasFactory, HasPublicUuid;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::saving(function (self $setting): void {
            $setting->boundary_key = implode(':', [$setting->tenant_id, $setting->company_id ?? 0, $setting->campus_id ?? 0, $setting->institute_id ?? 0]);
        });
    }

    protected function casts(): array
    {
        return ['entity_key' => AcademicEntityKey::class, 'status' => AcademicRecordStatus::class];
    }
}
