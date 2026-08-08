<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Authentication\ActiveContext;
use App\Core\Modules\Module;
use App\Core\Settings\InterfacePreferenceResolver;
use App\Core\Settings\UiColourPalette;
use App\Core\Settings\UiFontFamily;
use App\Core\Settings\UiThemePreset;
use App\Models\User;

final class InterfaceLayoutResolver
{
    public function __construct(
        private InterfacePreferenceResolver $preferences,
    ) {}

    public function resolve(User $user, ActiveContext $context, ?Module $module = null): ResolvedInterfaceLayout
    {
        return $this->build($user, $context, $module);
    }

    private function build(User $user, ActiveContext $context, ?Module $module): ResolvedInterfaceLayout
    {
        $base = $this->preferences->resolve($user, $context);
        $portal = $context->portal;

        $userLayout = $module === null
            ? null
            : UserLayoutPreference::query()
                ->where('tenant_id', $context->membership->tenant_id)
                ->where('user_id', $user->id)
                ->where('portal_id', $context->portal->id)
                ->where('module_id', $module->id)
                ->first();

        $portalLayout = PortalLayoutSetting::query()
            ->where('tenant_id', $context->membership->tenant_id)
            ->where('portal_id', $context->portal->id)
            ->first();

        $tenantLayout = TenantLayoutSetting::query()->where('tenant_id', $context->membership->tenant_id)->first();

        return new ResolvedInterfaceLayout(
            user: $user,
            portal: $portal,
            academicYear: $context->academicYear,
            institute: $context->membership->accessScope->institute,
            sidebarPosition: $userLayout?->sidebar_position
                ?? $portalLayout?->sidebar_position
                ?? config('interface.defaults.sidebar_position', 'left'),
            sidebarState: $userLayout?->sidebar_state
                ?? $portalLayout?->sidebar_state
                ?? config('interface.defaults.sidebar_state', 'auto'),
            sidebarWidth: $userLayout?->sidebar_width
                ?? config('interface.defaults.sidebar_width', 'standard'),
            sidebarAutoCollapse: $userLayout?->sidebar_auto_collapse ?? false,
            headerMode: $userLayout?->header_mode
                ?? $portalLayout?->header_mode
                ?? config('interface.defaults.header_mode', 'standard'),
            topbarMode: $portalLayout?->topbar_mode
                ?? config('interface.defaults.topbar_mode', 'visible'),
            topbarVisible: $userLayout?->topbar_visible ?? true,
            topbarClockFormat: $userLayout?->topbar_clock_format
                ?? config('interface.defaults.topbar_clock_format', 'long_date_time_12'),
            showSeconds: $userLayout?->show_seconds ?? true,
            contentWidth: $userLayout?->content_width
                ?? $portalLayout?->content_width
                ?? $base->contentWidth,
            contentDensity: $userLayout?->content_density
                ?? $base->interfaceDensity,
            footerMode: $userLayout?->footer_mode
                ?? $portalLayout?->footer_mode
                ?? config('interface.defaults.footer_mode', 'standard'),
            themePreset: $userLayout?->theme_preset_id ? UiThemePreset::find($userLayout->theme_preset_id) : $base->themePreset,
            colourPalette: $userLayout?->colour_palette_id ? UiColourPalette::find($userLayout->colour_palette_id) : $base->primaryPalette,
            fontFamily: $userLayout?->font_family_id ? UiFontFamily::find($userLayout->font_family_id) : $base->fontFamily,
            fontSize: $userLayout?->font_size,
            lineHeight: $userLayout?->line_height ?? $base->lineHeight,
            borderRadius: $userLayout?->border_radius ?? $base->cardRadius,
            tableDensity: $userLayout?->table_density ?? $base->tablePreferences['table_density'] ?? 'comfortable',
            formDensity: $userLayout?->form_density ?? 'standard',
            showMenuIcons: $userLayout?->show_menu_icons ?? true,
            showBreadcrumbs: $userLayout?->show_breadcrumbs ?? true,
            showQuickActions: $userLayout?->show_quick_actions ?? true,
            animationLevel: $userLayout?->animation_level ?? ($base->accessibilityPreferences['reduced_motion'] ? 'reduced' : 'standard'),
            reducedMotion: $userLayout?->reduced_motion ?? $base->accessibilityPreferences['reduced_motion'],
            highContrast: $userLayout?->high_contrast ?? $base->accessibilityPreferences['high_contrast'],
            locale: $user->locale ?? $context->membership->tenant->locale ?? config('app.locale'),
            timezone: $context->membership->tenant->timezone ?? config('app.timezone'),
            metadata: [
                'tenant_id' => $context->membership->tenant_id,
                'portal_id' => $context->portal->id,
                'module_id' => $module?->id,
                'layout_version' => 1,
            ],
        );
    }
}
