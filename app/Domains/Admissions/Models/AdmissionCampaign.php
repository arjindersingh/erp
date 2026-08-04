<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Models;

use App\Core\Organization\Institute;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Admissions\Enums\AdmissionCampaignStatus;
use Database\Factories\AdmissionCampaignFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(AdmissionCampaignFactory::class)]
final class AdmissionCampaign extends AdmissionDomainModel
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => AdmissionCampaignStatus::class,
            'application_opens_at' => 'immutable_datetime',
            'application_closes_at' => 'immutable_datetime',
            'submission_deadline_at' => 'immutable_datetime',
            'settings' => 'array',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(AdmissionCampaignOffering::class, 'campaign_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(AdmissionApplication::class, 'campaign_id');
    }

    public function acceptsApplications(): bool
    {
        $now = now();

        return $this->status === AdmissionCampaignStatus::Open
            && $this->application_opens_at?->lessThanOrEqualTo($now)
            && $this->application_closes_at?->greaterThanOrEqualTo($now);
    }
}
