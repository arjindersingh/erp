<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Services;

use App\Domains\Admissions\Enums\AdmissionApplicationSource;
use App\Domains\Admissions\Enums\AdmissionApplicationStatus;
use App\Domains\Admissions\Models\AdmissionApplication;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class StartAdmissionApplication
{
    /** @param array<string, mixed> $data @return array{application: AdmissionApplication, access_token: string} */
    public function handle(AdmissionCampaign $campaign, array $data, AdmissionApplicationSource $source = AdmissionApplicationSource::PublicOnline): array
    {
        if (! $campaign->acceptsApplications()) {
            throw ValidationException::withMessages(['campaign' => 'This admission campaign is not accepting applications.']);
        }

        $token = Str::random(64);

        $application = DB::transaction(fn (): AdmissionApplication => AdmissionApplication::query()->create([
            'tenant_id' => $campaign->tenant_id,
            'company_id' => $campaign->company_id,
            'campus_id' => $campaign->campus_id,
            'institute_id' => $campaign->institute_id,
            'academic_year_id' => $campaign->academic_year_id,
            'campaign_id' => $campaign->id,
            'source' => $source,
            'applicant_given_name' => $data['given_name'],
            'applicant_family_name' => $data['family_name'] ?? null,
            'applicant_date_of_birth' => $data['date_of_birth'] ?? null,
            'applicant_email' => isset($data['email']) ? mb_strtolower(trim((string) $data['email'])) : null,
            'applicant_mobile' => $data['mobile'] ?? null,
            'identity_fingerprint' => $this->fingerprint($campaign, $data),
            'access_token_hash' => hash('sha256', $token),
            'status' => AdmissionApplicationStatus::Draft,
        ]));

        return ['application' => $application, 'access_token' => $token];
    }

    /** @param array<string, mixed> $data */
    private function fingerprint(AdmissionCampaign $campaign, array $data): string
    {
        return hash('sha256', implode('|', [
            $campaign->tenant_id,
            $campaign->id,
            mb_strtolower(trim((string) ($data['given_name'] ?? ''))),
            (string) ($data['date_of_birth'] ?? ''),
            mb_strtolower(trim((string) ($data['email'] ?? ''))),
            preg_replace('/\D+/', '', (string) ($data['mobile'] ?? '')),
        ]));
    }
}
