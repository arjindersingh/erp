<footer class="border-t border-slate-200 bg-white py-4">
    <div class="mx-auto flex max-w-[calc(100%-2rem)] flex-wrap items-center justify-between gap-3 text-sm text-slate-500 px-4 sm:px-6">
        <p>© {{ now()->year }} {{ config('app.name', 'Education ERP') }} | ERP v{{ config('app.version', '0.1.0') }}</p>
        <div class="flex flex-wrap items-center gap-3">
            <a href="#" class="hover:text-cyan-700">Privacy</a>
            <a href="#" class="hover:text-cyan-700">Terms</a>
            <span class="text-slate-400">|</span>
            <span>Support</span>
        </div>
    </div>
</footer>
