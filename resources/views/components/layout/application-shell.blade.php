@props(['layout', 'modules' => collect(), 'profileLabel' => null])

<div class="min-h-screen bg-slate-50 text-slate-900">
    <x-layout.top-bar :layout="$layout" :modules="$modules" />
    <x-layout.header :layout="$layout" :modules="$modules" />

    <div class="mx-auto flex min-h-[calc(100vh-7rem)] max-w-full flex-col lg:flex-row" style="--sidebar-width: {{ $layout->sidebarWidth === 'compact' ? '4.5rem' : ($layout->sidebarWidth === 'wide' ? '20rem' : '16rem') }}; --content-max-width: {{ $layout->contentWidth === 'full' ? '100%' : ($layout->contentWidth === 'wide' ? '110rem' : ($layout->contentWidth === 'boxed' ? '1120px' : '90rem')) }};">
        <x-layout.sidebar :layout="$layout" :modules="$modules" :profile-label="$profileLabel" />

        <div class="flex min-h-screen flex-1 flex-col bg-slate-50 p-4 lg:p-6" style="margin-left: {{ $layout->sidebarPosition === 'left' ? '0' : '0' }};">
            <x-layout.breadcrumbs :layout="$layout" />
            <div class="mb-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                {{ $slot }}
            </div>
        </div>
    </div>

    <x-layout.footer :layout="$layout" />
</div>
