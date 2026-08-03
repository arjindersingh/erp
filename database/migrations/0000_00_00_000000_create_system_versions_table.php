<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('version')->index();
            $table->string('build')->default('foundation');
            $table->string('commit_hash', 64)->nullable()->index();
            $table->timestamp('deployed_at')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['version', 'build']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_versions');
    }
};
