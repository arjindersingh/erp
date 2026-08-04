<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration dashboard</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<main class="mx-auto max-w-7xl px-6 py-10">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Administration portal</p>
                <h1 class="mt-2 text-3xl font-bold">Welcome to the administration shell</h1>
                <p class="mt-2 text-sm text-slate-600">The dashboard is now wired to the resolved tenant, active context, and admissions foundation.</p>
            </div>
            <div class="rounded-2xl border border-cyan-100 bg-cyan-50 px-4 py-3 text-sm font-semibold text-cyan-800">
                {{ $tenant->name }} · {{ $context->portal->name }}
            </div>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="font-semibold">Active context</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $context->academicYear->name }} · {{ $context->scope->name }}</p>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="font-semibold">Admissions campaigns</h2>
                <p class="mt-2 text-3xl font-bold text-cyan-700">{{ $campaigns }}</p>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="font-semibold">Profile</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $profiles->person?->display_name ?? 'No linked person' }}</p>
            </section>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('admin.access-diagnostics') }}" class="rounded-xl bg-cyan-700 px-4 py-2 font-semibold text-white">Open access diagnostics</a>
            <a href="{{ route('profile') }}" class="rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700">Profile</a>
            <a href="{{ route('logout') }}" class="rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700">Logout</a>
        </div>
    </div>
</main>
</body>
</html>
