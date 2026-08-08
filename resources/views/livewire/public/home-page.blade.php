<div class="min-h-screen bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.32em] text-cyan-700">{{ $tenant?->name ?? config('app.name') }}</p>
                <h1 class="text-2xl font-bold">ERP portal shell</h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admissions.public.index') }}" class="rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700">Admissions</a>
                <a href="{{ $staffEntryUrl }}" class="rounded-xl bg-cyan-700 px-4 py-2 font-semibold text-white">{{ $staffEntryLabel }}</a>
                @auth
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-slate-300 px-4 py-2 font-semibold text-slate-700">Sign out</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-14">
        <section class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Public homepage</p>
                <h2 class="mt-4 text-4xl font-bold tracking-tight">A secure entrance to the campus and admissions experience.</h2>
                <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-600">The homepage now resolves the tenant context, surfaces the open admissions campaigns, and exposes the login and admissions entry points for visitors and applicants.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('admissions.public.index') }}" class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white">Apply for admission</a>
                    <a href="{{ $staffEntryUrl }}" class="rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700">{{ $staffEntryLabel }}</a>
                    @auth
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700">Sign out</button>
                        </form>
                    @endauth
                </div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h3 class="text-xl font-semibold">Open campaigns</h3>
                @forelse($campaigns as $campaign)
                    <article class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-cyan-700">{{ $campaign->institute?->name ?? 'Institute' }}</p>
                        <h4 class="mt-1 font-semibold">{{ $campaign->name }}</h4>
                        <p class="mt-2 text-sm text-slate-600">Closes {{ $campaign->application_closes_at?->toFormattedDateString() ?? 'soon' }}</p>
                    </article>
                @empty
                    <p class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">No open campaigns are available right now.</p>
                @endforelse
            </div>
        </section>
    </main>
</div>
