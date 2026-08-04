<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

final class FrontendShellAuditCommand extends Command
{
    protected $signature = 'erp:frontend-shell-audit';

    protected $description = 'Audit the implemented frontend shell routes and entry points';

    public function handle(): int
    {
        $checks = [
            'home' => route('home'),
            'login' => route('login'),
            'logout' => route('logout'),
            'profile' => route('profile'),
            'admin.dashboard' => route('admin.dashboard'),
            'admin.access-diagnostics' => route('admin.access-diagnostics'),
            'admissions.public.index' => route('admissions.public.index'),
        ];

        $failures = 0;
        foreach ($checks as $name => $target) {
            if ($target === null) {
                $this->error("FAIL {$name}: route missing.");
                $failures++;
                continue;
            }

            $this->info("PASS {$name}: {$target}");
        }

        if ($failures === 0) {
            $this->info('CERTIFIED WITH CONDITIONS: core shell routes are available.');

            return self::SUCCESS;
        }

        return self::FAILURE;
    }
}
