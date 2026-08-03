<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\System\SystemHealthService;
use Illuminate\Contracts\View\View;

final class SystemHealthController extends Controller
{
    public function __invoke(SystemHealthService $health): View
    {
        return view('system.health', ['report' => $health->inspect()]);
    }
}
