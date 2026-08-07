<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Tenancy\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PlatformSetupController
{
    public function __invoke(Request $request): View
    {
        return view('platform.setup', [
            'tenantCount' => Tenant::query()->count(),
            'user' => $request->user(),
        ]);
    }
}
