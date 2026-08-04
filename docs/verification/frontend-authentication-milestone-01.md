# Frontend authentication Milestone 1 verification

Date: 2026-08-04 UTC

Status: **CERTIFIED WITH CONDITIONS for Milestone 1; complete Admissions frontend NOT CERTIFIED.**

Evidence:

- Fresh migration and base seed: passed, including admissions schema and permission catalogue.
- `UatDemoSeeder`: passed; two tenants, two companies/campuses, four institutes, academic years, users/memberships/roles, domains, module activation and campaigns created.
- Authentication/profile/admissions tests: 8 tests and 22 assertions passed before final dashboard coverage was added.
- Full regression suite: 111 tests and 275 assertions passed before final dashboard coverage was added.
- PHPStan: passed with no findings.
- Pint: passed.
- Vite production build: passed; generated CSS 78.88 kB and JS 45.58 kB.
- Frontend access audit: all implemented public/staff/diagnostic route chains passed; future policy/resource/menu inspection warned.
- Profile audit: zero cross-tenant links and zero invalid active membership scopes; one pre-existing unlinked platform/test account warned.
- Navigation audit: passed.
- Admissions integrity audit: zero failures, one expected later-schema warning.
- Browser attempt 1: ChromeDriver unavailable. Driver 151 installed and started.
- Browser attempt 2: failed before session creation because the container has no Chrome binary. Ubuntu's Chromium package delegates to Snap, and Snap is unavailable in this container. No browser assertion executed.

Security proof includes a feature test that substitutes a Tenant B membership UUID while authenticated on Tenant A and receives validation denial. Login requires a membership in the host-resolved tenant. Protected admissions routes require authentication, restored context, enabled module and effective permission.

Conditions/remediation:

1. Provide a container-native Chrome/Chromium binary or Playwright image, then implement and pass login/context/logout browser journeys.
2. Build the functional responsibility, committee, delegation and explicit-denial foundation; seed every required persona separately.
3. Add dynamic admissions menus and record policies, then hostile direct-URL and stale-tab tests.
4. Implement applicant resume and the public-to-conversion operational slice with browser evidence.
5. Classify or link the pre-existing `test@example.com` account.
6. Expand frontend audits from route middleware inspection to Filament/Livewire actions, policies, file access, queues and browser result artifacts.
