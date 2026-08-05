<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum HeaderMode: string
{
    case Standard = 'standard';
    case Compact = 'compact';
    case Minimal = 'minimal';
    case BrandingFocused = 'branding_focused';
    case SearchFocused = 'search_focused';
}
