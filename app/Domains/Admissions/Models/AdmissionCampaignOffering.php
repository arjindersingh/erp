<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Models;

use App\Domains\Academics\Models\AcademicClass;
use App\Domains\Academics\Models\ProgrammeOffering;
use App\Domains\Admissions\Enums\AdmissionOfferingType;
use Database\Factories\AdmissionCampaignOfferingFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(AdmissionCampaignOfferingFactory::class)]
final class AdmissionCampaignOffering extends AdmissionDomainModel
{
    use HasFactory;

    protected function casts(): array
    {
        return ['offering_type' => AdmissionOfferingType::class, 'settings' => 'array', 'is_active' => 'boolean'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdmissionCampaign::class, 'campaign_id');
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function programmeOffering(): BelongsTo
    {
        return $this->belongsTo(ProgrammeOffering::class);
    }
}
