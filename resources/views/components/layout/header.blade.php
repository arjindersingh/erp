<header class="sticky top-11 z-20 border-b border-slate-200 bg-white/95 backdrop-blur-xl">
    <div class="mx-auto flex max-w-[calc(100%-2rem)] items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <div class="flex items-center gap-4">
            <div class="brand-mark" aria-hidden="true">EH</div>
            <div>
                <h1 class="text-lg font-semibold text-slate-900">{{ config('app.name', 'Education ERP') }}</h1>
                <p class="text-sm text-slate-500">{{ $layout->portal->name }} | {{ $layout->academicYear->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="button-secondary">Quick create</button>
            <button type="button" class="button-secondary">Search</button>
        </div>
    </div>
</header>
