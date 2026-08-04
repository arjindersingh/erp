<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Domains\Academics\Enums\AffiliationStatus;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\InstituteAuthorityAffiliationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

#[UseFactory(InstituteAuthorityAffiliationFactory::class)]
final class InstituteAuthorityAffiliation extends Model
{
    use BelongsToTenant, HasFactory, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::saving(function (self $affiliation): void {
            $authorityTenant = EducationAuthority::withoutGlobalScopes()->whereKey($affiliation->education_authority_id)->value('tenant_id');
            if ($authorityTenant !== null && (int) $authorityTenant !== (int) $affiliation->tenant_id) {
                throw ValidationException::withMessages(['education_authority_id' => 'Authority belongs to another tenant.']);
            }
            if ($affiliation->valid_from && $affiliation->valid_until && $affiliation->valid_from->gt($affiliation->valid_until)) {
                throw ValidationException::withMessages(['valid_until' => 'Affiliation end date must not precede its start date.']);
            }
        });
    }

    protected function casts(): array
    {
        return ['status' => AffiliationStatus::class, 'valid_from' => 'date', 'valid_until' => 'date', 'approved_at' => 'datetime', 'is_primary' => 'boolean'];
    }

    public function authority(): BelongsTo
    {
        return $this->belongsTo(EducationAuthority::class, 'education_authority_id');
    }
}
