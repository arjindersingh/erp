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
                const parts = new Intl.DateTimeFormat('en-GB', {
                    timeZone: this.timezone,
                    day: '2-digit',
                    month: 'short',
                    year: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                    weekday: 'long',
                }).formatToParts(this.serverTime).reduce((values, part) => ({ ...values, [part.type]: part.value }), {});

                this.display = `${parts.day}-${parts.month}-${parts.year} (${parts.hour}:${parts.minute} ${parts.dayPeriod}) (${parts.weekday})`;
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
