<?php

declare(strict_types=1);

namespace Modules\Admissions\Http\Controllers;

use App\Core\Attribution\ActorContext;
use App\Core\Attribution\ActorContextResolver;
use App\Core\Attribution\PublicAccessIdentity;
use App\Domains\Admissions\Models\AdmissionCampaign;
use App\Domains\Admissions\Services\StartAdmissionApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $identity = PublicAccessIdentity::query()->firstOrCreate(
            [
                'tenant_id' => $campaign->tenant_id,
                'identifier_hash' => hash('sha256', mb_strtolower(trim((string) ($validated['email'] ?? $validated['mobile'] ?? 'unknown')))),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'identity_type' => 'email',
                'masked_identifier' => isset($validated['email']) ? substr((string) $validated['email'], 0, 2).'***@'.explode('@', (string) $validated['email'], 2)[1] : 'mobile',
                'verification_method' => 'public_form',
                'verification_status' => 'verified',
                'verified_at' => now(),
                'expires_at' => now()->addHours(24),
            ],
        );

        $request->attributes->set('public_access_identity', $identity);
        $request->attributes->set('public_access_identity_id', $identity->getKey());

        $context = app(ActorContextResolver::class)->resolveFromRequest($request);
        app()->instance(ActorContext::class, $context);
        $request->attributes->set('actor_context', $context);

        $result = $action->handle($campaign, $validated);

        return redirect()->route('admissions.public.apply', $campaign)
            ->with('application_started', [
                'reference' => $result['application']->uuid,
                'access_token' => $result['access_token'],
            ]);
    }
}
