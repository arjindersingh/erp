<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('system_versions')) {
            return;
        }

        $missingColumns = [
            'uuid' => ! Schema::hasColumn('system_versions', 'uuid'),
            'commit_hash' => ! Schema::hasColumn('system_versions', 'commit_hash'),
            'deployed_at' => ! Schema::hasColumn('system_versions', 'deployed_at'),
            'metadata' => ! Schema::hasColumn('system_versions', 'metadata'),
        ];

        if (in_array(true, $missingColumns, true)) {
            Schema::table('system_versions', function (Blueprint $table) use ($missingColumns): void {
                if ($missingColumns['uuid']) {
                    $table->uuid('uuid')->nullable()->unique();
                }

                if ($missingColumns['commit_hash']) {
                    $table->string('commit_hash', 64)->nullable();
                }

                if ($missingColumns['deployed_at']) {
                    $table->timestamp('deployed_at')->nullable();
                }

                if ($missingColumns['metadata']) {
                    $table->json('metadata')->nullable();
                }
            });
        }

        DB::table('system_versions')
            ->whereNull('uuid')
            ->orderBy('id')
            ->each(fn (object $version) => DB::table('system_versions')
                ->where('id', $version->id)
                ->update(['uuid' => (string) Str::uuid()]));
    }

    public function down(): void
    {
        // Existing installations may rely on these fields after the upgrade.
    }
};
