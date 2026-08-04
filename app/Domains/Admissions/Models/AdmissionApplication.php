<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Models;

use App\Domains\Admissions\Enums\AdmissionApplicationSource;
use App\Domains\Admissions\Enums\AdmissionApplicationStatus;
use Database\Factories\AdmissionApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(AdmissionApplicationFactory::class)]
final class AdmissionApplication extends AdmissionDomainModel
{
    use HasFactory;

    protected $hidden = ['access_token_hash'];

    protected function casts(): array
    {
        return [
            'source' => AdmissionApplicationSource::class,
            'status' => AdmissionApplicationStatus::class,
            'received_at' => 'immutable_datetime',
            'data_entry_completed_at' => 'immutable_datetime',
            'applicant_confirmed_at' => 'immutable_datetime',
            'submitted_at' => 'immutable_datetime',
            'source_metadata' => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdmissionCampaign::class, 'campaign_id');
    }
}
