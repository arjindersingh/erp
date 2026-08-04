<?php

declare(strict_types=1);

namespace App\Core\Settings;

final readonly class ResolvedInterfacePreferences
{
    public function __construct(
        public string $brandName,
        public ?string $logoUrl,
        public ?string $compactLogoUrl,
        public ?string $faviconUrl,
        public string $appearanceMode,
        public ?UiThemePreset $themePreset,
        public ?UiFontFamily $fontFamily,
        public string $fontScale,
        public string $lineHeight,
        public ?UiColourPalette $primaryPalette,
        public ?UiColourPalette $secondaryPalette,
        public string $interfaceDensity,
        public string $sidebarMode,
        public string $navigationStyle,
        public string $contentWidth,
        public string $cardRadius,
        public array $tablePreferences,
        public array $dashboardPreferences,
        public array $accessibilityPreferences,
    ) {}

    public function safeInlineTokenString(): string
    {
        return sprintf(
            '--ui-font-family: %s; --ui-font-scale: %s; --ui-line-height: %s; --ui-density-factor: %s; --ui-card-radius: %s; --ui-content-max-width: %s;',
            e($this->fontFamily?->css_font_family ?? config('interface.font_families.system.css_font_family')),
            e($this->fontScale),
            e($this->lineHeight),
            e(config('interface.interface_densities')[$this->interfaceDensity] ?? 1),
            e(config('interface.card_radii')[$this->cardRadius] ?? '0.75rem'),
            e(config('interface.content_widths')[$this->contentWidth] ?? '90rem'),
        );
    }
}
