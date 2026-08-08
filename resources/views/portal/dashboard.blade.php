<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $content['title'] }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased">
<x-layout.application-shell :layout="$layout" :modules="$modules" :profile-label="$profileLabel">
<main class="mx-auto max-w-7xl px-2 py-3 sm:px-4">
    <header class="rounded-3xl bg-gradient-to-br from-cyan-500 via-sky-600 to-indigo-700 p-7 text-white shadow-2xl sm:p-10">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-cyan-100">{{ $content['eyebrow'] }}</p>
                <h1 class="mt-3 text-3xl font-bold sm:text-4xl">{{ $content['title'] }}</h1>
                <p class="mt-3 text-base text-cyan-50">{{ $content['description'] }}</p>
            </div>
            <a href="{{ route('context.select') }}" class="rounded-xl border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/20">Switch portal</a>
        </div>
        <div class="mt-8 flex flex-wrap gap-x-5 gap-y-2 text-sm text-cyan-50">
            <span>{{ $tenant->name }}</span><span>•</span><span>{{ $context->scope->name }}</span><span>•</span><span>{{ $context->academicYear->name }}</span>
        </div>
    </header>

    <section class="mt-7 grid gap-4 sm:grid-cols-3">
        @foreach($stats as $stat)
            <article class="rounded-2xl bg-white p-5 shadow-lg shadow-slate-900/10">
                <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-7 grid gap-5 lg:grid-cols-3">
        @foreach($content['cards'] as $card)
            <article class="rounded-2xl bg-white p-6 shadow-lg shadow-slate-900/10">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-lg font-bold text-cyan-700">{{ strtoupper(substr($card['title'], 0, 1)) }}</div>
                <h2 class="mt-5 text-lg font-bold text-slate-900">{{ $card['title'] }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $card['description'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-7 flex flex-wrap gap-3">
        <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-white px-4 py-2 font-semibold text-slate-800 shadow hover:bg-slate-100">Administration</a>
        <a href="{{ route('profile') }}" class="rounded-xl border border-slate-600 px-4 py-2 font-semibold text-white hover:bg-slate-800">My profile</a>
    </section>
</main>
</x-layout.application-shell>
</body>
</html>
