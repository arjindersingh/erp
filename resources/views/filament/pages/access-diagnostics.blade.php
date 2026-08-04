<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access diagnostics</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<main class="mx-auto max-w-6xl px-6 py-10">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-bold">Access diagnostics</h1>
        <p class="mt-3 text-sm text-slate-600">This page surfaces the resolved tenant, portal, scope, and profile context and is restricted to authenticated users with access to the administration shell.</p>
        <div class="mt-8 grid gap-5 md:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="font-semibold">Resolved context</h2>
                <dl class="mt-3 space-y-3 text-sm text-slate-600">
                    <div><dt class="font-semibold text-slate-800">Tenant</dt><dd>{{ $tenantName }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Scope</dt><dd>{{ $scopeName }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Portal</dt><dd>{{ $portalName }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Academic year</dt><dd>{{ $academicYear }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Person</dt><dd>{{ $personName }}</dd></div>
                </dl>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="font-semibold">Status</h2>
                <p class="mt-3 text-sm text-slate-600">The diagnostics page is now available through the administration shell and will expand as more profile and navigation services are implemented.</p>
            </section>
        </div>
    </div>
</main>
</body>
</html>
