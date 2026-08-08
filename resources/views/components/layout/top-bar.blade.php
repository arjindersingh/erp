@props(['layout', 'modules' => collect()])

<div class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur-xl">
    <div class="mx-auto flex h-11 max-w-[calc(100%-2rem)] items-center justify-between gap-4 px-4 text-sm text-slate-700 sm:px-6">
        <div class="flex flex-wrap items-center gap-3 text-slate-700">
            <form method="post" action="{{ route('context.academic-year.update') }}">
                @csrf
                <label for="academic-session" class="sr-only">Academic session</label>
                <select id="academic-session" name="academic_year" class="rounded-lg border-slate-300 bg-white py-1 pl-2 pr-8 text-sm font-semibold text-slate-800 shadow-sm focus:border-cyan-600 focus:ring-cyan-600" onchange="this.form.submit()">
                    @foreach($academicYears as $academicYear)
                        <option value="{{ $academicYear->uuid }}" @selected($academicYear->uuid === $layout->academicYear->uuid)>{{ $academicYear->name }}</option>
                    @endforeach
                </select>
            </form>
            <x-layout.live-clock :layout="$layout" />
            <span>{{ $layout->timezone }}</span>
        </div>
        <div class="flex items-center gap-2">
            <x-layout.theme-selector :layout="$layout" />
            <x-layout.module-launcher :layout="$layout" :modules="$modules" />
            <x-layout.account-menu :layout="$layout" />
        </div>
    </div>
</div>
