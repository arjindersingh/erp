<?php

declare(strict_types=1);

namespace App\Core\Modules;

enum ModuleType: string
{
    case Core = 'core';
    case Academic = 'academic';
    case Administrative = 'administrative';
    case Financial = 'financial';
    case HumanResource = 'human_resource';
    case StudentService = 'student_service';
    case Communication = 'communication';
    case Public = 'public';
    case Reporting = 'reporting';
    case Integration = 'integration';
}
