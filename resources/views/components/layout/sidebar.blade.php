@props(['layout', 'modules' => collect(), 'profileLabel' => null])

<aside class="order-first lg:sticky lg:top-[calc(4.5rem+1px)] lg:z-20 lg:flex lg:h-[calc(100vh-8rem)] lg:w-[var(--sidebar-width)] lg:flex-col" aria-label="Application navigation">
    <div class="hidden lg:flex lg:h-full lg:flex-col lg:gap-4 lg:border-r lg:border-slate-200 lg:bg-white lg:px-3 lg:py-4">
        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-800">
                <p>Navigation</p>
                <p class="mt-1 text-xs font-normal text-slate-500">{{ $profileLabel ?? $layout->portal->name }}</p>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('portal.dashboard') }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-slate-100">Dashboard</a>
                @foreach($modules as $module)
                    <a href="{{ $module->default_route_name && Route::has($module->default_route_name) ? route($module->default_route_name) : route('portal.dashboard') }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-slate-100">{{ $module->short_name ?: $module->name }}</a>
                @endforeach
                <a href="{{ route('profile') }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-slate-100">My profile</a>
                <a href="{{ route('context.select') }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-slate-100">Switch workspace</a>
            </nav>
        </div>
    </div>
</aside>
