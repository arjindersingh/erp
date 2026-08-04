<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Providers\PanelAccessService;
use Illuminate\Console\Command;

final class PanelAccessAuditCommand extends Command
{
    protected $signature = 'erp:panel-access-audit';

    protected $description = 'Audit panel access checks for the shell';

    public function handle(PanelAccessService $access): int
    {
        $user = User::query()->first();
        if ($user === null) {
            $this->info('No user available for panel access audit.');

            return self::SUCCESS;
        }

        $this->info('Admin access check: '.($access->canAccess($user, 'administration') ? 'allowed' : 'denied'));

        return self::SUCCESS;
    }
}
