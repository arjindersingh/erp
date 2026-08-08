@php
    $person = $layout->user->persons()
        ->wherePivot('tenant_id', $layout->metadata['tenant_id'])
        ->orderByPivot('is_primary', 'desc')
        ->first();
    $avatar = $person?->photo_path;
    $avatarUrl = $avatar ? (str_starts_with($avatar, 'http') ? $avatar : \Illuminate\Support\Facades\Storage::url($avatar)) : null;
    $initials = collect(preg_split('/\s+/', trim($person?->display_name ?: $layout->user->name)))
        ->filter()->map(fn (string $name) => strtoupper(substr($name, 0, 1)))->take(2)->implode('');
@endphp

<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
    <button type="button" class="account-avatar" @click="open = !open" :aria-expanded="open.toString()" aria-controls="account-menu" aria-label="Open account menu" title="{{ $layout->user->name }}">
        @if($avatarUrl)<img src="{{ $avatarUrl }}" alt="{{ $layout->user->name }}">@else<span>{{ $initials }}</span>@endif
    </button>
    <div id="account-menu" x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false" class="topbar-popover right-0 w-56" role="menu">
        <p class="px-1 text-sm font-semibold text-slate-800">{{ $person?->display_name ?: $layout->user->name }}</p>
        <p class="mt-1 px-1 text-xs text-slate-500">{{ $layout->user->email }}</p>
        <div class="my-3 border-t border-slate-100"></div>
        <a href="{{ route('profile') }}" class="account-menu-link">My profile</a>
        <a href="{{ route('context.select') }}" class="account-menu-link">Switch workspace</a>
        <form method="post" action="{{ route('logout') }}" class="mt-1">@csrf<button type="submit" class="account-menu-link w-full text-left text-rose-600">Sign out</button></form>
    </div>
</div>
