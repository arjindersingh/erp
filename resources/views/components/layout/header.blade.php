@props(['layout', 'modules' => collect()])

@php
    $activeModule = $modules->first(fn ($module) => $module->default_route_name === request()->route()?->getName());
    $contextLabel = $layout->portal->name.($activeModule ? ' ('.($activeModule->short_name ?: $activeModule->name).')' : '');
@endphp

<header class="sticky top-11 z-20 border-b border-slate-200 bg-white/95 backdrop-blur-xl">
    <div class="mx-auto flex max-w-[calc(100%-2rem)] items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <div class="flex items-center gap-4">
            <div class="brand-mark" aria-hidden="true">EH</div>
            <div>
                <h1 class="text-base font-semibold text-slate-900">{{ $contextLabel }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="button-secondary">Quick create</button>
            <button type="button" class="button-secondary">Search</button>
        </div>
    </div>
</header>
