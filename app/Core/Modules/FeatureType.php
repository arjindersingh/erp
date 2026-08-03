<?php

declare(strict_types=1);

namespace App\Core\Modules;

enum FeatureType: string
{
    case Dashboard = 'dashboard';
    case Resource = 'resource';
    case Transaction = 'transaction';
    case Workflow = 'workflow';
    case Report = 'report';
    case Configuration = 'configuration';
    case Utility = 'utility';
    case Integration = 'integration';
    case PublicPage = 'public_page';
}
