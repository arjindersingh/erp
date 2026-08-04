<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class ProfileResolutionAuditCommand extends Command
{
    protected $signature = 'erp:profile-resolution-audit';

    protected $description = 'Audit user-person-profile and membership resolution integrity';

    public function handle(): int
    {
        $crossTenant = DB::table('user_person_links as l')->join('persons as p', 'p.id', '=', 'l.person_id')->whereColumn('l.tenant_id', '<>', 'p.tenant_id')->count();
        $invalidMemberships = DB::table('user_memberships as m')->leftJoin('access_scopes as s', function ($j): void {
            $j->on('s.id', '=', 'm.access_scope_id')->on('s.tenant_id', '=', 'm.tenant_id');
        })->where('m.status', 'active')->whereNull('s.id')->count();
        foreach (['Cross-tenant person links' => $crossTenant, 'Active memberships with invalid scope' => $invalidMemberships] as $label => $count) {
            $this->line(($count ? 'FAIL' : 'PASS')." {$label}: {$count}.");
        }
        $unlinked = DB::table('users')->leftJoin('user_person_links', 'users.id', '=', 'user_person_links.user_id')->whereNull('user_person_links.id')->count();
        $this->line("WARNING Users without person links: {$unlinked} (service/platform accounts require classification review).");

        return ($crossTenant + $invalidMemberships) === 0 ? self::SUCCESS : self::FAILURE;
    }
}
