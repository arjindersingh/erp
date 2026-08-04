<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Tenancy\TenantScope;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

final class DataOwnershipAuditCommand extends Command
{
    protected $signature = 'erp:data-ownership-audit';

    protected $description = 'Audit tenant-owned models for mandatory ownership controls';

    public function handle(): int
    {
        $failures = 0;

        foreach (config('tenancy.owned_models', []) as $modelClass) {
            /** @var Model $model */
            $model = new $modelClass;
            $table = $model->getTable();

            $failures += $this->check(
                Schema::hasColumn($table, 'tenant_id'),
                "$table contains tenant_id",
                "$table is missing tenant_id",
            );

            $failures += $this->check(
                $this->hasTenantIndex($table),
                "$table has a tenant-leading index",
                "$table is missing a tenant-leading index",
            );

            $scopes = $model->getGlobalScopes();
            $failures += $this->check(
                collect($scopes)->contains(fn (object $scope): bool => $scope instanceof TenantScope),
                "$modelClass uses TenantScope",
                "$modelClass is missing TenantScope",
            );
        }

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function check(bool $passes, string $pass, string $fail): int
    {
        $passes ? $this->info("PASS  $pass") : $this->error("FAIL  $fail");

        return $passes ? 0 : 1;
    }

    private function hasTenantIndex(string $table): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['columns'][0] ?? null) === 'tenant_id') {
                return true;
            }
        }

        return false;
    }
}
