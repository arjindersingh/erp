<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_event_definitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('event_code')->unique();
            $table->string('category')->index();
            $table->string('action')->index();
            $table->string('default_severity')->index();
            $table->string('title_template');
            $table->text('summary_template')->nullable();
            $table->boolean('is_security_event')->default(false)->index();
            $table->boolean('is_sensitive')->default(false)->index();
            $table->boolean('is_required')->default(false)->index();
            $table->unsignedBigInteger('retention_policy_id')->nullable()->index();
            $table->unsignedBigInteger('notification_rule_id')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('institute_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();

            $table->string('actor_type')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('persons')->restrictOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained('user_memberships')->restrictOnDelete();
            $table->foreignId('access_scope_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('role_assignment_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('role_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('portal_id')->nullable()->constrained()->restrictOnDelete();

            $table->foreignId('module_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('module_feature_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('module_code')->nullable()->index();
            $table->string('feature_code')->nullable()->index();

            $table->string('category')->index();
            $table->string('action')->index();
            $table->string('severity')->index();
            $table->string('outcome')->index();

            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->uuid('subject_uuid')->nullable()->index();
            $table->string('subject_label')->nullable();

            $table->string('event_code')->index();
            $table->string('event_title');
            $table->text('event_summary')->nullable();
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();

            $table->json('old_values_json')->nullable();
            $table->json('new_values_json')->nullable();
            $table->json('changed_fields_json')->nullable();
            $table->json('metadata_json')->nullable();

            $table->uuid('request_id')->nullable()->index();
            $table->uuid('correlation_id')->nullable()->index();
            $table->uuid('batch_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('job_id')->nullable()->index();
            $table->unsignedBigInteger('api_token_id')->nullable()->index();

            $table->string('source')->index();
            $table->string('route_name')->nullable()->index();
            $table->string('request_method', 10)->nullable();
            $table->text('request_url')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('forwarded_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('operating_system')->nullable();

            $table->timestamp('occurred_at')->index();
            $table->timestamp('recorded_at')->useCurrent()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable()->index();
            $table->char('integrity_hash', 64)->nullable()->index();
            $table->char('previous_hash', 64)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_type', 'subject_id'], 'audit_logs_subject_index');
            $table->index(['tenant_id', 'occurred_at'], 'audit_logs_tenant_time_index');
            $table->index(['tenant_id', 'user_id', 'occurred_at'], 'audit_logs_tenant_user_time_index');
            $table->index(['tenant_id', 'subject_type', 'subject_id'], 'audit_logs_tenant_subject_index');
            $table->index(['tenant_id', 'event_code', 'occurred_at'], 'audit_logs_tenant_event_time_index');
            $table->index(['tenant_id', 'severity', 'occurred_at'], 'audit_logs_tenant_severity_time_index');
            $table->index(['tenant_id', 'category', 'occurred_at'], 'audit_logs_tenant_category_time_index');
        });

        Schema::create('audit_log_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_log_id')->constrained()->cascadeOnDelete();
            $table->string('field_name')->index();
            $table->string('field_label')->nullable();
            $table->text('old_value_text')->nullable();
            $table->text('new_value_text')->nullable();
            $table->char('old_value_hash', 64)->nullable();
            $table->char('new_value_hash', 64)->nullable();
            $table->string('data_type')->nullable();
            $table->boolean('is_sensitive')->default(false)->index();
            $table->boolean('is_masked')->default(false)->index();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['audit_log_id', 'field_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log_changes');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('audit_event_definitions');
    }
};
