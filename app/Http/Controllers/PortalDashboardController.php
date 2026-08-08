<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Authentication\ActiveContext;
use App\Core\Authentication\AuthenticatedProfileResolver;
use App\Core\Identity\UserMembership;
use App\Core\Layout\InterfaceLayoutResolver;
use App\Core\Modules\ModuleNavigationResolver;
use App\Core\Tenancy\TenantContext;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PortalDashboardController
{
    public function __invoke(Request $request, TenantContext $tenantContext, ActiveContext $context, AuthenticatedProfileResolver $profiles, ModuleNavigationResolver $moduleNavigation): View
    {
        $tenant = $tenantContext->requireTenant();
        $portal = $context->portal;
        $content = $this->contentFor($portal->code);
        $modules = $moduleNavigation->forContext($request->user(), $tenant, $context);
        $profileSet = $profiles->resolveFor($request->user(), $tenant);
        $profileLabel = $profileSet->person?->display_name ?? $request->user()->name;
        $layout = app(InterfaceLayoutResolver::class)->resolve($request->user(), $context);

        $stats = [
            ['label' => 'Active memberships', 'value' => UserMembership::withoutGlobalScopes()->where('tenant_id', $tenant->id)->selectable()->count()],
            ['label' => 'Available modules', 'value' => $modules->count()],
            ['label' => 'Admissions campaigns', 'value' => AdmissionCampaign::query()->where('tenant_id', $tenant->id)->count()],
        ];

        return view('portal.dashboard', compact('content', 'context', 'layout', 'modules', 'portal', 'profileLabel', 'stats', 'tenant'));
    }

    /** @return array{eyebrow: string, title: string, description: string, cards: array<int, array{title: string, description: string}>} */
    private function contentFor(string $portal): array
    {
        return match ($portal) {
            'site_admin' => ['eyebrow' => 'Platform control', 'title' => 'Site administration', 'description' => 'Monitor the platform, tenant access, security, and shared configuration.', 'cards' => [
                ['title' => 'Platform health', 'description' => 'Review services, jobs, and operational signals.'],
                ['title' => 'Access governance', 'description' => 'Manage roles, permissions, and administrative controls.'],
                ['title' => 'Tenant oversight', 'description' => 'Review tenant configuration and platform-wide activity.'],
            ]],
            'management' => ['eyebrow' => 'Leadership workspace', 'title' => 'Management dashboard', 'description' => 'See the organisation’s operational picture and act on priorities.', 'cards' => [
                ['title' => 'Organisation performance', 'description' => 'Track key academic and operational indicators.'],
                ['title' => 'Approvals', 'description' => 'Review decisions that require management attention.'],
                ['title' => 'Reports', 'description' => 'Open consolidated management reporting.'],
            ]],
            'staff' => ['eyebrow' => 'Staff workspace', 'title' => 'Staff dashboard', 'description' => 'Keep daily work, requests, and assigned responsibilities in one place.', 'cards' => [
                ['title' => 'My work', 'description' => 'Review assigned tasks and follow-ups.'],
                ['title' => 'Admissions', 'description' => 'Work with active campaigns and applications.'],
                ['title' => 'Profile & preferences', 'description' => 'Maintain your working profile and interface settings.'],
            ]],
            'teacher' => ['eyebrow' => 'Teaching workspace', 'title' => 'Teacher dashboard', 'description' => 'Plan classes, follow learner progress, and manage teaching work.', 'cards' => [
                ['title' => 'Today’s classes', 'description' => 'See your teaching schedule and course responsibilities.'],
                ['title' => 'Learner progress', 'description' => 'Review attendance, outcomes, and interventions.'],
                ['title' => 'Academic delivery', 'description' => 'Manage coursework, subjects, and classroom activity.'],
            ]],
            'student' => ['eyebrow' => 'Student workspace', 'title' => 'Student dashboard', 'description' => 'Stay on top of coursework, schedules, and campus services.', 'cards' => [
                ['title' => 'My learning', 'description' => 'View courses, subjects, and upcoming learning activity.'],
                ['title' => 'Schedule', 'description' => 'See classes, assessments, and calendar events.'],
                ['title' => 'Student services', 'description' => 'Access transport and other available services.'],
            ]],
            'parent' => ['eyebrow' => 'Family workspace', 'title' => 'Parent dashboard', 'description' => 'Follow your child’s learning, communication, and services.', 'cards' => [
                ['title' => 'Child overview', 'description' => 'Review learner information and current academic activity.'],
                ['title' => 'Updates', 'description' => 'Read notices, messages, and upcoming events.'],
                ['title' => 'Transport', 'description' => 'Track available transport information and requests.'],
            ]],
            'alumni' => ['eyebrow' => 'Alumni workspace', 'title' => 'Alumni dashboard', 'description' => 'Stay connected with your institution and alumni community.', 'cards' => [
                ['title' => 'Community', 'description' => 'Discover alumni news, events, and opportunities.'],
                ['title' => 'Profile', 'description' => 'Keep your professional and contact details current.'],
                ['title' => 'Engagement', 'description' => 'Explore ways to contribute to the institution.'],
            ]],
            default => ['eyebrow' => 'Institution operations', 'title' => 'Administration dashboard', 'description' => 'Manage academic operations, admissions, people, and institutional settings.', 'cards' => [
                ['title' => 'Academic operations', 'description' => 'Manage academic years, structures, programmes, and subjects.'],
                ['title' => 'Admissions', 'description' => 'Review campaigns and application activity.'],
                ['title' => 'Access & settings', 'description' => 'Manage users, permissions, and organisation settings.'],
            ]],
        };
    }
}
