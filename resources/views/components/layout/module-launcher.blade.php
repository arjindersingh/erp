@props(['layout', 'modules' => collect()])

<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
    <button type="button" class="topbar-icon-button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="module-menu" aria-label="Open module menu" title="Modules">
        <span class="grid grid-cols-3 gap-0.5" aria-hidden="true">@for($dot = 0; $dot < 9; $dot++)<i class="h-1 w-1 rounded-full bg-current"></i>@endfor</span>
    </button>
    <div id="module-menu" x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false" class="topbar-popover right-0 w-80" role="menu">
        <div class="flex items-center justify-between px-1"><p class="text-sm font-semibold text-slate-800">Applications</p><span class="text-xs text-slate-400">{{ $modules->count() }} available</span></div>
        <div class="mt-3 grid grid-cols-3 gap-2">
            @forelse($modules as $module)
                <a href="{{ route($module->default_route_name) }}" class="module-app-tile" title="{{ $module->name }}">
                    <span class="module-app-icon module-app-icon-{{ $loop->index % 6 }}">{{ strtoupper(substr($module->short_name ?: $module->name, 0, 1)) }}</span>
                    <span>{{ $module->short_name ?: $module->name }}</span>
                </a>
            @empty
                <p class="col-span-3 rounded-xl bg-slate-50 p-4 text-center text-sm text-slate-500">No modules are available in this workspace.</p>
            @endforelse
        </div>
    </div>
</div>
