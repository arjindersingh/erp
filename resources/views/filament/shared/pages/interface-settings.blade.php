<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Interface Settings</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<main class="mx-auto max-w-5xl px-4 py-10">
    <div class="space-y-8">
        <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-3xl font-semibold">Interface Settings</h1>
            <p class="mt-2 text-sm text-slate-600">Manage your appearance, typography, layout, accessibility and theme preferences.</p>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-xl font-semibold">Appearance</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Appearance mode</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="system">System</option>
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Theme preset</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="default">Default</option>
                            <option value="academic_blue">Academic Blue</option>
                            <option value="professional_navy">Professional Navy</option>
                            <option value="modern_teal">Modern Teal</option>
                            <option value="minimal_neutral">Minimal Neutral</option>
                            <option value="soft_green">Soft Green</option>
                            <option value="high_contrast">High Contrast</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Accent palette</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="blue">Blue</option>
                            <option value="teal">Teal</option>
                            <option value="purple">Purple</option>
                            <option value="neutral">Neutral</option>
                        </select>
                    </div>
                </div>
            </article>

            <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-xl font-semibold">Typography</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Font family</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="system">System UI</option>
                            <option value="inter">Inter</option>
                            <option value="roboto">Roboto</option>
                            <option value="open_sans">Open Sans</option>
                            <option value="source_sans_3">Source Sans 3</option>
                            <option value="noto_sans">Noto Sans</option>
                            <option value="atkinson_hyperlegible">Atkinson Hyperlegible</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Font scale</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="0.90">Small</option>
                            <option value="1.00">Default</option>
                            <option value="1.10">Large</option>
                            <option value="1.25">Extra Large</option>
                            <option value="1.40">Maximum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Line height</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="compact">Compact</option>
                            <option value="normal">Normal</option>
                            <option value="relaxed">Relaxed</option>
                        </select>
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-xl font-semibold">Layout</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Interface density</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="compact">Compact</option>
                            <option value="comfortable">Comfortable</option>
                            <option value="spacious">Spacious</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Sidebar mode</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="auto">Automatic</option>
                            <option value="expanded">Expanded</option>
                            <option value="collapsed">Collapsed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Content width</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="standard">Standard</option>
                            <option value="wide">Wide</option>
                            <option value="full">Full</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Card radius</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                            <option value="square">Square</option>
                            <option value="soft">Soft</option>
                            <option value="rounded">Rounded</option>
                        </select>
                    </div>
                </div>
            </article>

            <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-xl font-semibold">Accessibility</h2>
                <div class="mt-4 space-y-4">
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                        <input type="checkbox" class="mt-1 h-5 w-5 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span>
                            <span class="font-semibold text-slate-900">High contrast</span>
                            <span class="block text-sm text-slate-600">Increase contrast for text, buttons, and alerts.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                        <input type="checkbox" class="mt-1 h-5 w-5 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span>
                            <span class="font-semibold text-slate-900">Reduced motion</span>
                            <span class="block text-sm text-slate-600">Minimize animations and motion effects.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                        <input type="checkbox" class="mt-1 h-5 w-5 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span>
                            <span class="font-semibold text-slate-900">Enhanced focus</span>
                            <span class="block text-sm text-slate-600">Improve focus outlines for keyboard navigation.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                        <input type="checkbox" class="mt-1 h-5 w-5 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <span>
                            <span class="font-semibold text-slate-900">Underline links</span>
                            <span class="block text-sm text-slate-600">Show underlines on links for easier recognition.</span>
                        </span>
                    </label>
                </div>
            </article>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-xl font-semibold">Preview</h2>
            <div class="mt-6 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-cyan-700">Live preview</p>
                            <h3 class="text-2xl font-semibold text-slate-900">This is how your interface will look.</h3>
                        </div>
                        <div class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-700">Example</div>
                    </div>
                    <div class="space-y-6">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h4 class="text-lg font-semibold">Heading</h4>
                            <p class="mt-3 text-sm leading-6 text-slate-600">A sample paragraph demonstrates the selected font and line height. The interface preview does not save until you click Save Preferences.</p>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <button class="button-primary w-full">Primary button</button>
                            <button class="button-secondary w-full">Secondary button</button>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-900">Table sample</span>
                                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Striped</span>
                            </div>
                            <table class="min-w-full text-left text-sm text-slate-600">
                                <thead class="border-b border-slate-200 text-slate-900">
                                    <tr>
                                        <th class="py-3 pr-6">Item</th>
                                        <th class="py-3 pr-6">Status</th>
                                        <th class="py-3">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white">
                                        <td class="py-3 pr-6">Sample row</td>
                                        <td class="py-3 pr-6">Active</td>
                                        <td class="py-3">Enabled</td>
                                    </tr>
                                    <tr class="bg-slate-50">
                                        <td class="py-3 pr-6">Second row</td>
                                        <td class="py-3 pr-6">Pending</td>
                                        <td class="py-3">Review</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <button class="button-secondary w-full rounded-2xl px-5 py-3 text-left sm:w-auto">Cancel Unsaved Changes</button>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button class="button-secondary rounded-2xl px-5 py-3">Reset to defaults</button>
                <button class="button-primary rounded-2xl px-5 py-3">Save Preferences</button>
            </div>
        </section>
    </div>
</main>
</body>
</html>
