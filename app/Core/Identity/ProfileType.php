<?php

declare(strict_types=1);

namespace App\Core\Identity;

enum ProfileType: string
{
    case Management = 'management';
    case Employee = 'employee';
    case Teacher = 'teacher';
    case Student = 'student';
    case Guardian = 'guardian';
    case Alumni = 'alumni';
    case Service = 'service';
}
