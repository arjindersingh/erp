<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Navigation\Portal;
use App\Core\Organization\Institute;
use App\Core\Settings\Enums\ClockFormat;
use App\Core\Settings\Enums\ContentWidth;
use App\Core\Settings\Enums\FooterMode;
use App\Core\Settings\Enums\HeaderMode;
use App\Core\Settings\Enums\InterfaceDensity;
use App\Core\Settings\Enums\SidebarPosition;
use App\Core\Settings\Enums\SidebarState;
use App\Core\Settings\Enums\SidebarWidth;
use App\Core\Settings\Enums\TopbarMode;
use App\Core\Settings\Enums\AnimationLevel;
use App\Core\Settings\UiColourPalette;
use App\Core\Settings\UiFontFamily;
use App\Core\Settings\UiThemePreset;
use App\Domains\Academics\Models\AcademicYear;
use App\Models\User;

final readonly class ResolvedInterfaceLayout
{
    public function __construct(
        public User $user,
        public Portal $portal,
        public AcademicYear $academicYear,
        public ?Institute $institute,
        public string $sidebarPosition,
        public string $sidebarState,
        public string $sidebarWidth,
        public bool $sidebarAutoCollapse,
        public string $headerMode,
        public string $topbarMode,
        public bool $topbarVisible,
        public string $topbarClockFormat,
        public bool $showSeconds,
        public string $contentWidth,
        public string $contentDensity,
        public string $footerMode,
        public ?UiThemePreset $themePreset,
        public ?UiColourPalette $colourPalette,
        public ?UiFontFamily $fontFamily,
        public ?string $fontSize,
        public ?string $lineHeight,
        public ?string $borderRadius,
        public string $tableDensity,
        public string $formDensity,
        public bool $showMenuIcons,
        public bool $showBreadcrumbs,
        public bool $showQuickActions,
        public string $animationLevel,
        public bool $reducedMotion,
        public bool $highContrast,
        public string $locale,
        public string $timezone,
        public array $metadata,
    ) {}
}
