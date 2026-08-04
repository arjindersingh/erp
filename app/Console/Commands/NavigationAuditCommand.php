<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Authorization\Permission;
use App\Core\Navigation\MenuItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

final class NavigationAuditCommand extends Command
{
    protected $signature = 'erp:navigation-audit';

    protected $description = 'Validate menu routes and permission references';

    public function handle(): int
    {
        $failures = 0;
        foreach (MenuItem::query()->cursor() as $item) {
            if ($item->route_name && ! Route::has($item->route_name)) {
                $this->error("FAIL  Menu {$item->title} references missing route {$item->route_name}");
                $failures++;
            }
            if ($item->permission_code && ! Permission::query()->where('code', $item->permission_code)->exists()) {
                $this->error("FAIL  Menu {$item->title} references missing permission {$item->permission_code}");
                $failures++;
            }
        }
        if ($failures === 0) {
            $this->info('PASS  All menu routes and permissions are valid');
        }

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
