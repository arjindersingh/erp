<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

final class FrontendAccessAuditCommand extends Command
{
    protected $signature = 'erp:frontend-access-audit';

    protected $description = 'Audit authentication and context middleware on frontend routes';

    public function handle(): int
    {
        $failures = 0;
        $requirements = [
            'admissions.staff.dashboard' => ['auth', 'active-context', 'module-enabled:admissions', 'effective-permission:admissions.dashboard.view'],
            'admin.access-diagnostics' => ['auth', 'active-context', 'effective-permission:access.diagnostics.use'],
        ];
        foreach ($requirements as $name => $required) {
            $route = Route::getRoutes()->getByName($name);
            if ($route === null) {
                $this->line("FAIL {$name}: route missing.");
                $failures++;

                continue;
            }
            $middleware = $route->gatherMiddleware();
            foreach ($required as $item) {
                if (! in_array($item, $middleware, true)) {
                    $this->line("FAIL {$name}: missing {$item}.");
                    $failures++;
                }
            }
            if ($failures === 0) {
                $this->line("PASS {$name}: authentication, context, module and permission chain present.");
            }
        }
        foreach (['admissions.public.index', 'admissions.public.apply', 'admissions.public.applications.store'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $middleware = $route?->gatherMiddleware() ?? [];
            if ($route === null || ! in_array('module-enabled:admissions', $middleware, true)) {
                $this->line("FAIL {$name}: public module gate missing.");
                $failures++;
            } else {
                $this->line("PASS {$name}: tenant and module gated.");
            }
        }
        $this->line('WARNING policy/resource/menu/action inspection will expand with their frontend milestones.');

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
