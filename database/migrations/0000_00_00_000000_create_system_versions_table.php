<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_versions', function (Blueprint $table) {
            // Force the modern transactional MySQL engine.
            $table->engine = 'InnoDB';

            $table->id();
            $table->uuid('uuid')->unique();

            // Version values do not need VARCHAR(255).
            $table->string('version', 50);
            $table->string('build', 50);
            $table->string('commit_hash', 64)->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->json('metadata')->nullable();

            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(
                ['version', 'build'],
                'system_versions_version_build_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_versions');
    }
};
