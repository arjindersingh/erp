<?php

declare(strict_types=1);

namespace App\Http\Livewire\Public;

use App\Core\Tenancy\TenantContext;
use App\Domains\Admissions\Enums\AdmissionCampaignStatus;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
final class HomePage extends Component
{
    public function render(): View
    {
        $tenant = app(TenantContext::class)->tenant();
        $isAuthenticated = auth()->check();
        $staffEntryUrl = ! $isAuthenticated
            ? route('login')
            : ($tenant === null
                ? route('platform.setup')
                : (request()->session()->has('active_context') ? route('portal.dashboard') : route('context.select')));
        $staffEntryLabel = $isAuthenticated ? 'Open staff portal' : 'Staff login';
        $campaigns = AdmissionCampaign::query()
            ->with(['institute', 'academicYear'])
            ->where('status', AdmissionCampaignStatus::Open)
            ->where('application_opens_at', '<=', now())
            ->where('application_closes_at', '>=', now())
            ->latest('application_closes_at')
            ->limit(3)
            ->get();

        return view('livewire.public.home-page', compact('tenant', 'campaigns', 'staffEntryLabel', 'staffEntryUrl'));
    }
}
