<div class="relative" x-data="themeMenu()" @keydown.escape.window="open = false">
    <button type="button" class="topbar-icon-button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="theme-menu" aria-label="Choose theme" title="Choose theme">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3a9 9 0 1 0 9 9c0-1.1-.9-2-2-2h-1.4a1.6 1.6 0 0 1-1.4-2.4l.6-1A2.3 2.3 0 0 0 14.8 3H12Z"/><circle cx="7.5" cy="11" r=".7" fill="currentColor"/><circle cx="10" cy="7.5" r=".7" fill="currentColor"/><circle cx="14" cy="7.5" r=".7" fill="currentColor"/></svg>
    </button>
    <div id="theme-menu" x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false" class="topbar-popover right-0 w-64" role="menu">
        <p class="px-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Appearance</p>
        <div class="mt-3 grid grid-cols-2 gap-2">
            <button type="button" class="theme-choice theme-system" @click="setTheme('system')"><span>◐</span>System</button>
            <button type="button" class="theme-choice theme-light" @click="setTheme('light')"><span>☀</span>Light</button>
            <button type="button" class="theme-choice theme-dark" @click="setTheme('dark')"><span>☾</span>Dark</button>
            <button type="button" class="theme-choice theme-blue" @click="setTheme('blue')"><span></span>Blue</button>
            <button type="button" class="theme-choice theme-red" @click="setTheme('red')"><span></span>Red</button>
            <button type="button" class="theme-choice theme-green" @click="setTheme('green')"><span></span>Green</button>
            <button type="button" class="theme-choice theme-orange" @click="setTheme('orange')"><span></span>Orange</button>
            <button type="button" class="theme-choice theme-violet" @click="setTheme('violet')"><span></span>Violet</button>
            <button type="button" class="theme-choice theme-rose" @click="setTheme('rose')"><span></span>Rose</button>
            <button type="button" class="theme-choice theme-teal" @click="setTheme('teal')"><span></span>Teal</button>
            <button type="button" class="theme-choice theme-contrast" @click="setTheme('contrast')"><span>◑</span>Contrast</button>
        </div>
    </div>
</div>
