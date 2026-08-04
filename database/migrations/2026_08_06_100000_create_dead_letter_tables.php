<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dead_letter_messages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('outbox_message_id')->constrained('outbox_messages')->cascadeOnDelete();
            $table->foreignId('domain_event_id')->nullable()->constrained('domain_events')->nullOnDelete();
            $table->string('reason');
            $table->json('payload_json');
            $table->string('status')->default('recorded');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dead_letter_messages');
    }
};
