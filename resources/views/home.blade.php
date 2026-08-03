<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A secure, multi-tenant ERP platform for schools and colleges.">
    <meta name="theme-color" content="#071a2d">

    <title>Home · {{ config('app.name', 'Education ERP') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="home-shell min-h-screen bg-[#f4f8fb] text-slate-950 antialiased">
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <div class="relative isolate overflow-hidden">
        <div class="hero-grid absolute inset-0 -z-20" aria-hidden="true"></div>
        <div class="hero-glow hero-glow-one" aria-hidden="true"></div>
        <div class="hero-glow hero-glow-two" aria-hidden="true"></div>

        <header x-data="{ open: false }" class="relative z-50">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 lg:px-8" aria-label="Primary navigation">
                <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="{{ config('app.name', 'Education ERP') }} home">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 40 40" class="size-6" fill="none">
                            <path d="M8 13.5 20 7l12 6.5L20 20 8 13.5Z" fill="currentColor"/>
                            <path d="M11 18v8.5c5.6 4.2 12.4 4.2 18 0V18l-9 5-9-5Z" fill="currentColor" opacity=".68"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[.24em] text-cyan-700">One connected campus</span>
                        <span class="block text-lg font-bold tracking-tight text-slate-950">{{ config('app.name', 'Education ERP') }}</span>
                    </span>
                </a>

                <div class="hidden items-center gap-8 text-sm font-semibold text-slate-600 md:flex">
                    <a class="nav-link" href="#platform">Platform</a>
                    <a class="nav-link" href="#portals">Portals</a>
                    <a class="nav-link" href="#security">Security</a>
                    <a class="nav-link" href="#status">System status</a>
                </div>

                <div class="hidden md:block">
                    <a href="#platform" class="button-secondary">Explore the platform</a>
                </div>

                <button type="button" class="mobile-menu-button md:hidden" @click="open = ! open" :aria-expanded="open" aria-controls="mobile-navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <svg x-show="! open" viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg x-show="open" x-cloak viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>

                <div id="mobile-navigation" x-show="open" x-transition.origin.top class="mobile-navigation md:hidden" @click.outside="open = false">
                    <a href="#platform" @click="open = false">Platform</a>
                    <a href="#portals" @click="open = false">Portals</a>
                    <a href="#security" @click="open = false">Security</a>
                    <a href="#status" @click="open = false">System status</a>
                </div>
            </nav>
        </header>

        <main id="main-content">
            <section class="mx-auto grid min-h-[780px] max-w-7xl items-center gap-14 px-5 pb-24 pt-12 lg:grid-cols-[.92fr_1.08fr] lg:px-8 lg:pb-32 lg:pt-16">
                <div class="relative z-10">
                    <div class="hero-eyebrow home-reveal">
                        <span class="status-dot" aria-hidden="true"></span>
                        Built for schools, colleges, and education groups
                    </div>

                    <h1 class="home-reveal mt-7 max-w-3xl text-5xl font-semibold leading-[1.02] tracking-[-.055em] text-slate-950 sm:text-6xl lg:text-7xl" style="--reveal-delay: 90ms">
                        Run every campus from one <span class="text-gradient">clear view.</span>
                    </h1>

                    <p class="home-reveal mt-7 max-w-xl text-lg leading-8 text-slate-600" style="--reveal-delay: 180ms">
                        A secure, context-aware ERP that brings students, academics, finance, people, transport, and administration into one calm workspace.
                    </p>

                    <div class="home-reveal mt-9 flex flex-col gap-3 sm:flex-row" style="--reveal-delay: 270ms">
                        <a href="#platform" class="button-primary">
                            Discover the platform
                            <svg viewBox="0 0 20 20" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 10h12m-5-5 5 5-5 5"/></svg>
                        </a>
                        <a href="#portals" class="button-quiet">View role-based portals</a>
                    </div>

                    <dl class="home-reveal mt-12 grid max-w-xl grid-cols-3 gap-5 border-t border-slate-900/10 pt-7" style="--reveal-delay: 360ms">
                        <div><dt class="text-xs font-semibold uppercase tracking-[.16em] text-slate-500">Tenancy</dt><dd class="mt-2 text-base font-semibold">Isolated</dd></div>
                        <div><dt class="text-xs font-semibold uppercase tracking-[.16em] text-slate-500">Access</dt><dd class="mt-2 text-base font-semibold">Contextual</dd></div>
                        <div><dt class="text-xs font-semibold uppercase tracking-[.16em] text-slate-500">History</dt><dd class="mt-2 text-base font-semibold">Auditable</dd></div>
                    </dl>
                </div>

                <div class="home-reveal relative mx-auto w-full max-w-2xl lg:translate-x-5" style="--reveal-delay: 160ms">
                    <div class="dashboard-orbit orbit-one" aria-hidden="true"></div>
                    <div class="dashboard-orbit orbit-two" aria-hidden="true"></div>
                    <div class="dashboard-window">
                        <div class="dashboard-topbar">
                            <div class="flex items-center gap-2" aria-hidden="true"><span></span><span></span><span></span></div>
                            <p>Innocent Hearts Group <span>/</span> Loharan Campus</p>
                            <div class="dashboard-avatar">AS</div>
                        </div>
                        <div class="dashboard-body">
                            <aside class="dashboard-sidebar" aria-label="Dashboard preview navigation">
                                <div class="preview-logo"></div>
                                @foreach (['Dashboard', 'Students', 'Academics', 'Finance', 'Transport'] as $item)
                                    <div class="preview-nav-item {{ $loop->first ? 'is-active' : '' }}"><span></span><b>{{ $item }}</b></div>
                                @endforeach
                            </aside>
                            <div class="dashboard-content">
                                <div class="flex items-start justify-between gap-4">
                                    <div><p class="preview-kicker">Monday, 03 August</p><h2>Good morning, Aman.</h2><p>Here is what needs your attention today.</p></div>
                                    <span class="preview-session">2026–27</span>
                                </div>
                                <div class="preview-metrics">
                                    <article><span class="metric-icon cyan">↗</span><p>Students present</p><strong>1,842</strong><small>94.8% today</small></article>
                                    <article><span class="metric-icon amber">✓</span><p>Pending approvals</p><strong>18</strong><small>6 need review</small></article>
                                    <article><span class="metric-icon coral">⌁</span><p>Fee collection</p><strong>₹4.2L</strong><small>Today</small></article>
                                </div>
                                <div class="preview-lower-grid">
                                    <article class="chart-card">
                                        <div class="flex items-center justify-between"><div><p>Attendance trend</p><strong>Weekly overview</strong></div><span>Classes ▾</span></div>
                                        <div class="chart-bars" aria-label="Illustrative attendance chart">
                                            @foreach ([54, 70, 62, 82, 75, 91, 84] as $height)
                                                <i style="--bar-height: {{ $height }}%"></i>
                                            @endforeach
                                        </div>
                                    </article>
                                    <article class="activity-card"><p>Priority queue</p><strong>Ready for review</strong><ul><li><span class="cyan"></span>Admission approvals <b>08</b></li><li><span class="amber"></span>Leave requests <b>06</b></li><li><span class="coral"></span>Refund checks <b>04</b></li></ul></article>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="floating-card floating-card-left"><span>✓</span><div><small>Audit integrity</small><strong>Verified</strong></div></div>
                    <div class="floating-card floating-card-right"><span>18</span><div><small>Approvals</small><strong>In your queue</strong></div></div>
                </div>
            </section>
        </main>
    </div>

    <section id="platform" class="section-block bg-white">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <div class="section-heading scroll-reveal"><p>One connected foundation</p><h2>Less switching. More clarity.</h2><span>Each workspace adapts to the active tenant, campus, institute, role, portal, and academic year.</span></div>
            <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['01', 'Students & admissions', 'A dependable record from first enquiry through graduation, with scoped access at every step.'],
                    ['02', 'Academics & examination', 'Coordinate timetables, attendance, marks, results, and approvals from one academic context.'],
                    ['03', 'Finance & payroll', 'Separate preparation, verification, approval, and reversal with a durable financial history.'],
                    ['04', 'People & organisation', 'Model companies, campuses, institutes, memberships, and responsibilities without duplicate accounts.'],
                    ['05', 'Transport & services', 'Give staff, principals, parents, and students the right view of the same transport operation.'],
                    ['06', 'Audit & security', 'Understand who acted, what changed, where it happened, and whether the action succeeded.'],
                ] as [$number, $title, $description])
                    <article class="feature-card scroll-reveal">
                        <span>{{ $number }}</span><h3>{{ $title }}</h3><p>{{ $description }}</p><div class="feature-line"></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="portals" class="section-block portal-section">
        <div class="mx-auto grid max-w-7xl items-center gap-14 px-5 lg:grid-cols-[.8fr_1.2fr] lg:px-8">
            <div class="section-heading scroll-reveal text-left"><p>Role-based experiences</p><h2>One platform.<br>Many focused portals.</h2><span>Navigation stays familiar and uncluttered because people see the commands and records relevant to their work.</span></div>
            <div class="portal-grid scroll-reveal">
                @foreach ([['Management', 'Strategic insight across companies and campuses', 'M'], ['Administration', 'Approvals, operations, and institutional control', 'A'], ['Teacher', 'Classes, attendance, learning, and marks', 'T'], ['Parent', 'Children, fees, results, and transport', 'P'], ['Student', 'Timetable, learning, results, and services', 'S'], ['Alumni', 'Profile, documents, and community access', 'AL']] as [$title, $description, $initial])
                    <article><span>{{ $initial }}</span><div><h3>{{ $title }}</h3><p>{{ $description }}</p></div><b aria-hidden="true">→</b></article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="security" class="section-block bg-[#071a2d] text-white">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 lg:items-end">
                <div class="section-heading scroll-reveal text-left"><p class="!text-cyan-300">Security by design</p><h2 class="!text-white">Access that follows responsibility.</h2><span class="!text-slate-300">Permissions answer what someone may do. Scope and policy decide where—and to which record—it applies.</span></div>
                <div class="security-flow scroll-reveal" aria-label="Authorization resolution flow">
                    @foreach (['Tenant', 'Membership', 'Scope', 'Portal', 'Permission', 'Policy'] as $step)
                        <span>{{ $step }}</span>@if (! $loop->last)<i>→</i>@endif
                    @endforeach
                </div>
            </div>
            <div class="mt-14 grid gap-px overflow-hidden rounded-3xl bg-white/10 md:grid-cols-3">
                <article class="security-card scroll-reveal"><span>01</span><h3>Tenant isolated</h3><p>Every owned record stays inside a resolved tenant boundary.</p></article>
                <article class="security-card scroll-reveal"><span>02</span><h3>Explicitly authorised</h3><p>Menus improve usability; middleware and policies enforce access.</p></article>
                <article class="security-card scroll-reveal"><span>03</span><h3>Durably audited</h3><p>Meaningful business and security events create a searchable history.</p></article>
            </div>
        </div>
    </section>

    <section id="status" class="section-block bg-cyan-50">
        <div class="mx-auto max-w-5xl px-5 text-center lg:px-8">
            <div class="scroll-reveal"><span class="status-pill"><i></i> Platform services available</span><h2 class="mt-7 text-4xl font-semibold tracking-[-.04em] sm:text-5xl">A strong foundation, ready to grow.</h2><p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-slate-600">Check the live service endpoints or return to the top to explore the platform again.</p></div>
            <div class="scroll-reveal mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('api.health') }}" class="button-primary">API health <span aria-hidden="true">↗</span></a>
                <a href="{{ route('modules.health') }}" class="button-secondary">Module health <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>

    <footer class="bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-slate-900"><span class="brand-mark !size-8" aria-hidden="true"></span>{{ config('app.name', 'Education ERP') }}</a>
            <p>Secure operations for connected education.</p>
            <a href="#main-content" class="font-semibold text-slate-700 hover:text-cyan-700">Back to top ↑</a>
        </div>
    </footer>
</body>
</html>
