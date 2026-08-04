<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_change_requests', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('setting_definition_id')->constrained('setting_definitions')->cascadeOnDelete();
            $table->string('scope_type')->default('platform')->index();
            $table->unsignedBigInteger('scope_id')->nullable()->index();
            $table->json('current_value_json')->nullable();
            $table->json('proposed_value_json');
            $table->text('reason')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('decision')->nullable()->index();
            $table->text('review_remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'setting_definition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_change_requests');
    }
};