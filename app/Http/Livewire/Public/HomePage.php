<?php

declare(strict_types=1);

namespace App\Http\Livewire\Public;

use App\Core\Tenancy\TenantContext;
use App\Domains\Admissions\Models\AdmissionCampaign;
use App\Domains\Admissions\Enums\AdmissionCampaignStatus;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class HomePage extends Component
{
    public function render(): View
    {
        $tenant = app(TenantContext::class)->tenant();
        $campaigns = AdmissionCampaign::query()
            ->with(['institute', 'academicYear'])
            ->where('status', AdmissionCampaignStatus::Open)
            ->where('application_opens_at', '<=', now())
            ->where('application_closes_at', '>=', now())
            ->latest('application_closes_at')
            ->limit(3)
            ->get();

        return view('livewire.public.home-page', compact('tenant', 'campaigns'));
    }
}
