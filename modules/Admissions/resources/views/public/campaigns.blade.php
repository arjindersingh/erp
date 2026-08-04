<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admissions</title>@vite(['resources/css/app.css'])</head>
<body class="min-h-screen bg-slate-50 text-slate-900"><main class="mx-auto max-w-5xl px-6 py-12">
    <header class="mb-10"><p class="text-sm font-semibold uppercase tracking-widest text-indigo-600">Admissions</p><h1 class="mt-2 text-4xl font-bold">Open admission campaigns</h1><p class="mt-3 text-slate-600">Choose a campaign to review its dates and available academic offerings.</p></header>
    <div class="grid gap-5 md:grid-cols-2">
        @forelse ($campaigns as $campaign)
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">{{ $campaign->institute->name }}</p><h2 class="mt-1 text-xl font-semibold">{{ $campaign->name }}</h2><p class="mt-3 text-sm text-slate-600">Applications close {{ $campaign->application_closes_at->toDayDateTimeString() }}.</p><a class="mt-5 inline-flex rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white" href="{{ route('admissions.public.apply', $campaign) }}">View and apply</a></article>
        @empty
            <p class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">There are no open admission campaigns.</p>
        @endforelse
    </div>
</main></body></html>
