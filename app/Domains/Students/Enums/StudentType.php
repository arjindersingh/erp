<?php

declare(strict_types=1);

namespace App\Domains\Students\Enums;

enum StudentType: string
{
    case SchoolStudent = 'school_student';
    case CollegeStudent = 'college_student';
    case UniversityStudent = 'university_student';
    case DistanceLearner = 'distance_learner';
    case OnlineLearner = 'online_learner';
    case ExchangeStudent = 'exchange_student';
    case InternationalStudent = 'international_student';
    case ShortTermLearner = 'short_term_learner';
    case Other = 'other';
}
