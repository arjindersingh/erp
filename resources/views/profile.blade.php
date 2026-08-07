<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My profile</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<main class="mx-auto max-w-5xl px-6 py-10">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Authenticated profile</p>
                <h1 class="mt-2 text-3xl font-bold">{{ auth()->user()->name }}</h1>
                <p class="mt-2 text-sm text-slate-600">The profile view resolves the currently authenticated person and any linked employee, student, or guardian profile.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('profile.interface-settings') }}" class="rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700">Interface settings</a>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700">Sign out</button>
                </form>
            </div>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="font-semibold">Identity</h2>
                <dl class="mt-4 space-y-3 text-sm text-slate-600">
                    <div><dt class="font-semibold text-slate-800">Email</dt><dd>{{ auth()->user()->email }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Person</dt><dd>{{ $profiles->person?->display_name ?? 'No linked person' }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Employee</dt><dd>{{ $profiles->employee?->employee_number ?? 'None' }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Student</dt><dd>{{ $profiles->student?->student_number ?? 'None' }}</dd></div>
                    <div><dt class="font-semibold text-slate-800">Guardian</dt><dd>{{ $profiles->guardian?->guardian_number ?? 'None' }}</dd></div>
                </dl>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="font-semibold">Access context</h2>
                <p class="mt-3 text-sm text-slate-600">This view is intended to be used once the active context and membership selection flow has populated the session state.</p>
            </section>
        </div>
    </div>
</main>
</body>
</html>
