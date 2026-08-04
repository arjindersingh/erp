<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Domains\Academics\Services\AcademicStructureValidator;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class CanonicalAcademicModel extends Model
{
    use BelongsToTenant, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(fn (self $model) => app(AcademicStructureValidator::class)->validate($model));
    }
}
