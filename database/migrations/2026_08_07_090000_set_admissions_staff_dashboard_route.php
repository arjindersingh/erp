<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('modules')) {
            return;
        }

        DB::table('modules')
            ->where('code', 'admissions')
            ->update(['default_route_name' => 'admissions.staff.dashboard', 'updated_at' => now()]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('modules')) {
            return;
        }

        DB::table('modules')
            ->where('code', 'admissions')
            ->update(['default_route_name' => 'admissions.public.campaigns', 'updated_at' => now()]);
    }
};
