<?php

declare(strict_types=1);

namespace Modules\Admissions\Http\Controllers;

use App\Domains\Admissions\Models\AdmissionCampaign;
use App\Domains\Admissions\Services\StartAdmissionApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class PublicApplicationController
{
    public function store(Request $request, AdmissionCampaign $campaign, StartAdmissionApplication $action): RedirectResponse
    {
        $validated = $request->validate([
            'given_name' => ['required', 'string', 'max:120'],
            'family_name' => ['nullable', 'string', 'max:120'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'email' => ['nullable', 'email:rfc', 'max:254', 'required_without:mobile'],
            'mobile' => ['nullable', 'string', 'max:32', 'required_without:email'],
        ]);

        $result = $action->handle($campaign, $validated);

        return redirect()->route('admissions.public.apply', $campaign)
            ->with('application_started', [
                'reference' => $result['application']->uuid,
                'access_token' => $result['access_token'],
            ]);
    }
}
