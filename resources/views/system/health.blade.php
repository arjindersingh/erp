<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>System health · {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="mx-auto max-w-6xl px-5 py-12 lg:px-8">
        <div class="flex flex-col gap-6 border-b border-white/10 pb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.2em] text-cyan-300">Administrator diagnostics</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-[-.04em]">System health</h1>
                <p class="mt-3 text-slate-400">Checked {{ $report->checkedAt }}</p>
            </div>
            <span @class([
                'inline-flex w-fit items-center gap-2 rounded-full px-4 py-2 text-sm font-bold',
                'bg-emerald-400/10 text-emerald-300' => $report->status->value === 'healthy',
                'bg-amber-400/10 text-amber-300' => $report->status->value === 'warning',
                'bg-rose-400/10 text-rose-300' => $report->status->value === 'unhealthy',
            ])><i class="size-2 rounded-full bg-current"></i>{{ $report->status->label() }}</span>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-2">
            @foreach ($report->checks as $check)
                <article class="rounded-2xl border border-white/10 bg-white/[.04] p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div><h2 class="font-bold text-white">{{ $check->label }}</h2><p class="mt-2 text-sm leading-6 text-slate-400">{{ $check->summary }}</p></div>
                        <span @class([
                            'mt-1 size-2.5 shrink-0 rounded-full',
                            'bg-emerald-400' => $check->status->value === 'healthy',
                            'bg-amber-400' => $check->status->value === 'warning',
                            'bg-rose-400' => $check->status->value === 'unhealthy',
                        ])></span>
                    </div>
                    @if ($check->value !== null)<p class="mt-4 border-t border-white/10 pt-3 font-mono text-xs text-cyan-200">{{ $check->value }}</p>@endif
                </article>
            @endforeach
        </div>

        <a href="{{ route('home') }}" class="mt-8 inline-flex font-semibold text-cyan-300 hover:text-cyan-200">← Return home</a>
    </main>
</body>
</html>
