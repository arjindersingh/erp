<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Platform setup</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
<main class="mx-auto grid min-h-screen max-w-3xl place-items-center px-6 py-12">
    <section class="w-full rounded-3xl bg-white p-8 shadow-2xl sm:p-10">
        <p class="text-sm font-bold uppercase tracking-[0.24em] text-cyan-700">Platform setup</p>
        <h1 class="mt-3 text-3xl font-bold">{{ $tenantCount === 0 ? 'Create your first tenant' : 'Choose a tenant domain' }}</h1>
        <p class="mt-4 leading-7 text-slate-600">
            @if($tenantCount === 0)
                No organisation has been configured yet. You are signed in as {{ $user->name }}, but dashboards require a tenant, organisation scope, and tenant domain before data can be displayed safely.
            @else
                This central address does not identify a tenant. Open one of the configured tenant domains to enter its dashboard.
            @endif
        </p>

        <div class="mt-7 rounded-2xl bg-cyan-50 p-5 text-sm text-cyan-950">
            <p class="font-bold">Next step</p>
            <p class="mt-2">Create the first tenant and its domain through platform administration, then open that domain (for example, <code class="font-semibold">demo.erp.test</code>). The application will never guess a tenant from a central URL.</p>
        </div>

        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ route('home') }}" class="rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700">Return home</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="rounded-xl bg-cyan-800 px-4 py-2 font-semibold text-white">Sign out</button></form>
        </div>
    </section>
</main>
</body>
</html>
