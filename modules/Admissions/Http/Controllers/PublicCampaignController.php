<?php

declare(strict_types=1);

namespace Modules\Admissions\Http\Controllers;

use App\Domains\Admissions\Enums\AdmissionCampaignStatus;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Contracts\View\View;

final class PublicCampaignController
{
    public function index(): View
    {
        $campaigns = AdmissionCampaign::query()
            ->with(['institute', 'academicYear'])
            ->where('status', AdmissionCampaignStatus::Open)
            ->where('application_opens_at', '<=', now())
            ->where('application_closes_at', '>=', now())
            ->orderBy('application_closes_at')
            ->get();

        return view('admissions::public.campaigns', compact('campaigns'));
    }

    public function show(AdmissionCampaign $campaign): View
    {
        abort_unless($campaign->acceptsApplications(), 404);
        $campaign->load(['institute', 'academicYear', 'offerings.academicClass', 'offerings.programmeOffering']);

        return view('admissions::public.apply', compact('campaign'));
    }
}
