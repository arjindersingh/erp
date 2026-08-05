<div class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur-xl">
    <div class="mx-auto flex h-11 max-w-[calc(100%-2rem)] items-center justify-between gap-4 px-4 text-sm text-slate-700 sm:px-6">
        <div class="flex flex-wrap items-center gap-3 text-slate-700">
            <span class="font-semibold" aria-live="polite">{{ $layout->academicYear->name ?? '' }}</span>
            <span>{{ now()->timezone($layout->timezone)->format('l, j F Y') }}</span>
            <span aria-live="polite">{{ now()->timezone($layout->timezone)->format($layout->topbarClockFormat === 'time_only_24' ? 'H:i:s' : 'h:i:s A') }}</span>
            <span>{{ $layout->timezone }}</span>
        </div>
        <div class="flex items-center gap-2">
            <x-layout.theme-selector :layout="$layout" />
            <x-layout.module-launcher :layout="$layout" />
            <x-layout.account-menu :layout="$layout" />
        </div>
    </div>
</div>
