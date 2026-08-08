<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Authentication\ActiveContext;
use App\Models\User;

final class InterfacePreferenceResolver
{
    public function resolve(User $user, ActiveContext $context): ResolvedInterfacePreferences
    {
        $tenant = $context->membership->tenant;
        $portal = $context->portal;

        return $this->build($user, $tenant->id, $portal->id, $portal->code);
    }

    private function build(User $user, int $tenantId, int $portalId, string $portalCode): ResolvedInterfacePreferences
    {
        $defaults = config('interface.defaults');

        $tenantSettings = TenantInterfaceSetting::query()->where('tenant_id', $tenantId)->latest('updated_at')->first();
        $portalSettings = PortalInterfaceSetting::query()->where('tenant_id', $tenantId)->where('portal_id', $portalId)->first();
        $userPreferences = UserInterfacePreference::query()->where('tenant_id', $tenantId)->where('user_id', $user->id)->where('portal_id', $portalId)->first();

        $appearanceMode = $userPreferences?->appearance_mode ?? $portalSettings?->default_appearance_mode ?? $tenantSettings?->default_theme_preset_id ? $defaults['appearance_mode'] : $defaults['appearance_mode'];
        $fontScale = $userPreferences?->font_scale ?? $portalSettings?->default_font_scale ?? $defaults['font_scale'];
        $interfaceDensity = $userPreferences?->interface_density ?? $portalSettings?->default_density ?? $defaults['interface_density'];
        $sidebarMode = $userPreferences?->sidebar_mode ?? $portalSettings?->default_sidebar_mode ?? $defaults['sidebar_mode'];
        $contentWidth = $userPreferences?->content_width ?? $portalSettings?->default_content_width ?? $defaults['content_width'];

        return new ResolvedInterfacePreferences(
            brandName: $tenantSettings?->brand_name ?? $defaults['brand_name'] ?? config('app.name'),
            logoUrl: null,
            compactLogoUrl: null,
            faviconUrl: null,
            appearanceMode: $appearanceMode,
            themePreset: $userPreferences?->theme_preset_id ? UiThemePreset::find($userPreferences->theme_preset_id) : ($portalSettings?->default_theme_preset_id ? UiThemePreset::find($portalSettings->default_theme_preset_id) : null),
            fontFamily: $userPreferences?->font_family_id ? UiFontFamily::find($userPreferences->font_family_id) : null,
            fontScale: $fontScale,
            lineHeight: $userPreferences?->line_height ?? $defaults['line_height'],
            primaryPalette: $userPreferences?->primary_palette_id ? UiColourPalette::find($userPreferences->primary_palette_id) : null,
            secondaryPalette: null,
            interfaceDensity: $interfaceDensity,
            sidebarMode: $sidebarMode,
            navigationStyle: $userPreferences?->navigation_style ?? $portalSettings?->default_navigation_style ?? $defaults['navigation_style'],
            contentWidth: $contentWidth,
            cardRadius: $userPreferences?->card_radius ?? $defaults['card_radius'],
            tablePreferences: [
                'table_density' => $userPreferences?->table_density,
                'default_rows_per_page' => $userPreferences?->default_rows_per_page,
                'sticky_table_header' => $userPreferences?->sticky_table_header,
                'striped_table_rows' => $userPreferences?->striped_table_rows,
                'wrap_table_text' => $userPreferences?->wrap_table_text,
            ],
            dashboardPreferences: $userPreferences?->dashboard_preferences_json ?? [],
            accessibilityPreferences: [
                'high_contrast' => $userPreferences?->high_contrast ?? false,
                'reduced_motion' => $userPreferences?->reduced_motion ?? false,
                'enhanced_focus' => $userPreferences?->enhanced_focus ?? false,
                'large_click_targets' => $userPreferences?->large_click_targets ?? false,
                'underline_links' => $userPreferences?->underline_links ?? false,
                'dyslexia_friendly_font' => $userPreferences?->dyslexia_friendly_font ?? false,
                'reduced_transparency' => $userPreferences?->reduced_transparency ?? false,
                'simplified_layout' => $userPreferences?->simplified_layout ?? false,
            ],
        );
    }
}
