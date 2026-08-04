<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_groups', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('value_type')->default('string');
            $table->boolean('allows_tenant_values')->default(false);
            $table->boolean('allows_institute_values')->default(false);
            $table->boolean('allows_translations')->default(false);
            $table->boolean('is_hierarchical')->default(false);
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->unsignedInteger('display_order')->default(100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reference_values', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reference_group_id')->constrained('reference_groups')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('reference_values')->nullOnDelete();
            $table->string('code');
            $table->string('label');
            $table->string('short_label')->nullable();
            $table->text('description')->nullable();
            $table->string('external_code')->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            $table->unsignedInteger('display_order')->default(100);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_default')->default(false);
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('created_actor_type')->nullable()->index();
            $table->string('created_authentication_state')->nullable()->index();
            $table->string('created_via')->nullable()->index();
            $table->string('created_request_id')->nullable()->index();
            $table->string('created_correlation_id')->nullable()->index();
            $table->string('updated_actor_type')->nullable()->index();
            $table->string('updated_authentication_state')->nullable()->index();
            $table->string('updated_via')->nullable()->index();
            $table->string('updated_request_id')->nullable()->index();
            $table->string('updated_correlation_id')->nullable()->index();
            $table->string('deleted_actor_type')->nullable()->index();
            $table->string('deleted_authentication_state')->nullable()->index();
            $table->string('deleted_via')->nullable()->index();
            $table->string('deleted_request_id')->nullable()->index();
            $table->string('deleted_correlation_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['reference_group_id', 'code']);
        });

        Schema::create('domain_events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('aggregate_type')->nullable();
            $table->string('aggregate_id')->nullable();
            $table->string('aggregate_uuid')->nullable();
            $table->string('event_name');
            $table->unsignedInteger('event_version')->default(1);
            $table->json('payload_json');
            $table->json('metadata_json')->nullable();
            $table->json('actor_context_json')->nullable();
            $table->string('request_id')->nullable();
            $table->string('correlation_id')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('created_actor_type')->nullable()->index();
            $table->string('created_authentication_state')->nullable()->index();
            $table->string('created_via')->nullable()->index();
            $table->string('created_request_id')->nullable()->index();
            $table->string('created_correlation_id')->nullable()->index();
            $table->string('updated_actor_type')->nullable()->index();
            $table->string('updated_authentication_state')->nullable()->index();
            $table->string('updated_via')->nullable()->index();
            $table->string('updated_request_id')->nullable()->index();
            $table->string('updated_correlation_id')->nullable()->index();
            $table->string('deleted_actor_type')->nullable()->index();
            $table->string('deleted_authentication_state')->nullable()->index();
            $table->string('deleted_via')->nullable()->index();
            $table->string('deleted_request_id')->nullable()->index();
            $table->string('deleted_correlation_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('outbox_messages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('domain_event_id')->constrained('domain_events')->cascadeOnDelete();
            $table->string('topic');
            $table->string('event_name');
            $table->unsignedInteger('event_version')->default(1);
            $table->json('payload_json');
            $table->json('headers_json')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->string('status')->default('pending')->index();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('outbox_delivery_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('outbox_message_id')->constrained('outbox_messages')->cascadeOnDelete();
            $table->timestamp('attempted_at');
            $table->string('status')->default('attempted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_delivery_attempts');
        Schema::dropIfExists('outbox_messages');
        Schema::dropIfExists('domain_events');
        Schema::dropIfExists('reference_values');
        Schema::dropIfExists('reference_groups');
    }
};
