<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal shell</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<main class="mx-auto max-w-6xl px-6 py-10">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-bold">Portal shell</h1>
        <p class="mt-3 text-sm text-slate-600">This shell acts as the shared entry point for staff, teacher, student, guardian, and management workspaces.</p>
        <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach([['Administration', route('admin.dashboard')], ['Profile', route('profile')], ['Admissions', route('admissions.staff.dashboard')], ['Access diagnostics', route('admin.access-diagnostics')]] as [$label, $url])
                <a href="{{ $url }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-5 font-semibold text-slate-800 hover:border-cyan-300">{{ $label }}</a>
            @endforeach
        </div>
    </div>
</main>
</body>
</html>
