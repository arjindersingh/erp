<?php

declare(strict_types=1);

namespace App\Core\Settings;

final class InterfaceTokenGenerator
{
    public function generate(ResolvedInterfacePreferences $preferences): SafeInterfaceTokenSet
    {
        $tokens = [
            '--ui-font-family' => $preferences->fontFamily?->css_font_family ?? config('interface.font_families.system.css_font_family'),
            '--ui-font-scale' => $preferences->fontScale,
            '--ui-line-height' => (string) config('interface.line_heights')[$preferences->lineHeight] ?? '1.5',
            '--ui-density-factor' => (string) (config('interface.interface_densities')[$preferences->interfaceDensity] ?? 1),
            '--ui-card-radius' => config('interface.card_radii')[$preferences->cardRadius] ?? '0.75rem',
            '--ui-sidebar-width' => match ($preferences->sidebarMode) {
                'expanded' => '22rem',
                'collapsed' => '5.5rem',
                default => '17rem',
            },
            '--ui-content-max-width' => config('interface.content_widths')[$preferences->contentWidth] ?? '90rem',
            '--ui-motion-duration' => $preferences->accessibilityPreferences['reduced_motion'] ? '1ms' : '150ms',
            '--ui-focus-ring-width' => $preferences->accessibilityPreferences['enhanced_focus'] ? '0.28rem' : '0.18rem',
            '--ui-focus-ring-offset' => $preferences->accessibilityPreferences['enhanced_focus'] ? '0.25rem' : '0.1rem',
        ];

        return new SafeInterfaceTokenSet($tokens);
    }
}
