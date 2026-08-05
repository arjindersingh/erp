<?php

declare(strict_types=1);

namespace App\Core\Settings\Enums;

enum ClockFormat: string
{
    case LongDateTime12 = 'long_date_time_12';
    case LongDateTime24 = 'long_date_time_24';
    case ShortDateTime12 = 'short_date_time_12';
    case ShortDateTime24 = 'short_date_time_24';
    case TimeOnly12 = 'time_only_12';
    case TimeOnly24 = 'time_only_24';
}
