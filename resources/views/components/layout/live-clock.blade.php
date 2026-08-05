<div x-data="liveClock({
    serverTime: '{{ now()->timezone($layout->timezone)->toIso8601String() }}',
    timezone: '{{ $layout->timezone }}',
    format: '{{ $layout->topbarClockFormat }}',
    showSeconds: {{ $layout->showSeconds ? 'true' : 'false' }},
})" x-init="init()" x-text="display" aria-live="polite" class="font-mono"></div>

<script>
    function liveClock(config) {
        return {
            serverTime: new Date(config.serverTime),
            timezone: config.timezone,
            format: config.format,
            showSeconds: config.showSeconds,
            display: '',
            interval: null,

            init() {
                this.updateDisplay();
                this.interval = setInterval(() => this.tick(), 1000);
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        clearInterval(this.interval);
                    } else {
                        this.interval = setInterval(() => this.tick(), 1000);
                        this.sync();
                    }
                });
            },

            tick() {
                this.serverTime.setSeconds(this.serverTime.getSeconds() + 1);
                this.updateDisplay();
            },

            updateDisplay() {
                const options = {
                    hour: this.format.includes('24') ? '2-digit' : 'numeric',
                    minute: '2-digit',
                    second: this.showSeconds ? '2-digit' : undefined,
                    hour12: ! this.format.includes('24'),
                    weekday: this.format.startsWith('long') ? 'long' : undefined,
                    day: this.format.startsWith('long') ? 'numeric' : undefined,
                    month: this.format.startsWith('long') ? 'long' : undefined,
                    year: this.format.startsWith('long') ? 'numeric' : undefined,
                };
                this.display = new Intl.DateTimeFormat('en-US', options).format(this.serverTime);
            },

            async sync() {
                try {
                    const response = await fetch('/_clock-sync');
                    const payload = await response.json();
                    this.serverTime = new Date(payload.serverTime);
                    this.updateDisplay();
                } catch (error) {
                    console.warn('Clock sync failed', error);
                }
            },
        };
    }
</script>
