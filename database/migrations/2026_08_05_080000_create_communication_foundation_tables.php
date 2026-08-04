<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_channels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->boolean('supports_inbound')->default(false);
            $table->boolean('supports_outbound')->default(true);
            $table->boolean('supports_templates')->default(true);
            $table->boolean('supports_attachments')->default(false);
            $table->boolean('supports_replies')->default(false);
            $table->boolean('supports_delivery_status')->default(true);
            $table->boolean('supports_read_status')->default(false);
            $table->boolean('supports_scheduling')->default(false);
            $table->boolean('supports_bulk')->default(false);

            $table->boolean('is_system')->default(true);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('communication_provider_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('communication_channel_id')->constrained('communication_channels')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('configuration_schema_json')->nullable();
            $table->boolean('supports_oauth')->default(false);
            $table->boolean('supports_webhooks')->default(false);
            $table->boolean('supports_delivery_callbacks')->default(false);
            $table->boolean('is_system')->default(true);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_endpoint_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('communication_channel_id')->constrained('communication_channels')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('validation_rules_json')->nullable();
            $table->boolean('is_system')->default(true);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_purpose_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('default_sensitivity_level')->default('public');
            $table->boolean('requires_consent')->default(false);
            $table->boolean('allows_quiet_hour_override')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_purposes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->foreignId('communication_purpose_category_id')->constrained('communication_purpose_categories')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sensitivity_level')->default('public')->index();
            $table->boolean('requires_consent')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->boolean('allows_bulk')->default(false);
            $table->boolean('allows_scheduling')->default(true);
            $table->boolean('allows_fallback')->default(true);
            $table->boolean('allows_reply')->default(true);
            $table->boolean('is_system')->default(true);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_fallback_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('trigger_statuses_json')->nullable();
            $table->unsignedTinyInteger('maximum_attempts')->default(1);
            $table->unsignedInteger('delay_between_attempts_seconds')->default(0);
            $table->boolean('stop_on_delivery')->default(true);
            $table->boolean('stop_on_read')->default(true);
            $table->boolean('respect_recipient_preferences')->default(true);
            $table->boolean('respect_quiet_hours')->default(true);
            $table->boolean('allow_emergency_override')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_working_hours', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('timezone')->default('UTC');
            $table->json('working_days_json')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedBigInteger('holiday_calendar_id')->nullable();
            $table->boolean('allow_emergency_override')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_rate_limit_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedInteger('per_minute_limit')->nullable();
            $table->unsignedInteger('per_hour_limit')->nullable();
            $table->unsignedInteger('per_day_limit')->nullable();
            $table->unsignedInteger('recipient_daily_limit')->nullable();
            $table->unsignedInteger('bulk_batch_size')->nullable();
            $table->unsignedInteger('batch_delay_seconds')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_providers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('communication_channel_id')->constrained('communication_channels')->cascadeOnDelete();
            $table->foreignId('communication_provider_type_id')->constrained('communication_provider_types')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('configuration_json')->nullable();
            $table->string('secret_reference')->nullable();
            $table->boolean('is_platform_provider')->default(false);
            $table->boolean('is_tenant_provider')->default(false);
            $table->boolean('is_default')->default(false);
            $table->string('health_status')->default('unknown')->index();
            $table->timestamp('last_health_check_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('communication_endpoints', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('communication_provider_id')->nullable()->constrained('communication_providers')->nullOnDelete();
            $table->foreignId('communication_channel_id')->constrained('communication_channels')->cascadeOnDelete();
            $table->foreignId('communication_endpoint_type_id')->constrained('communication_endpoint_types')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('display_name');
            $table->string('address_or_identifier');
            $table->string('masked_identifier')->nullable();
            $table->foreignId('reply_to_endpoint_id')->nullable()->constrained('communication_endpoints')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('institute_id')->nullable()->constrained('institutes')->nullOnDelete();
            $table->unsignedBigInteger('functional_unit_id')->nullable();
            $table->boolean('supports_inbound')->default(false);
            $table->boolean('supports_outbound')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_method')->nullable();
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('priority')->default(100);
            $table->foreignId('working_hours_id')->nullable()->constrained('communication_working_hours')->nullOnDelete();
            $table->foreignId('rate_limit_policy_id')->nullable()->constrained('communication_rate_limit_policies')->nullOnDelete();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'communication_channel_id']);
        });

        Schema::create('communication_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('institute_id')->nullable()->constrained('institutes')->nullOnDelete();
            $table->unsignedBigInteger('functional_unit_id')->nullable();
            $table->unsignedBigInteger('default_language_id')->nullable();
            $table->unsignedBigInteger('fallback_language_id')->nullable();
            $table->foreignId('working_hours_id')->nullable()->constrained('communication_working_hours')->nullOnDelete();
            $table->foreignId('default_template_group_id')->nullable()->constrained('communication_template_groups')->nullOnDelete();
            $table->foreignId('rate_limit_policy_id')->nullable()->constrained('communication_rate_limit_policies')->nullOnDelete();
            $table->boolean('is_default')->default(false);
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'campus_id', 'institute_id']);
        });

        Schema::create('communication_profile_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_profile_id')->constrained('communication_profiles')->cascadeOnDelete();
            $table->foreignId('communication_channel_id')->constrained('communication_channels')->cascadeOnDelete();
            $table->foreignId('communication_endpoint_id')->constrained('communication_endpoints')->cascadeOnDelete();
            $table->unsignedInteger('priority')->default(100);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_reply_endpoint')->default(false);
            $table->boolean('is_fallback')->default(false);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();

            $table->unique(['communication_profile_id', 'communication_channel_id', 'communication_endpoint_id'], 'communication_profile_endpoint_unique');
        });

        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('communication_template_group_id')->nullable()->constrained('communication_template_groups')->nullOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->foreignId('communication_purpose_id')->nullable()->constrained('communication_purposes')->nullOnDelete();
            $table->foreignId('communication_channel_id')->nullable()->constrained('communication_channels')->nullOnDelete();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('communication_template_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('communication_template_id')->constrained('communication_templates')->cascadeOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->string('name');
            $table->text('subject')->nullable();
            $table->text('content_html')->nullable();
            $table->text('content_text')->nullable();
            $table->json('metadata_json')->nullable();
            $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('communication_template_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_required')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('communication_template_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_template_id')->constrained('communication_templates')->cascadeOnDelete();
            $table->foreignId('communication_template_version_id')->nullable()->constrained('communication_template_versions')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_routing_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('institute_id')->nullable()->constrained('institutes')->nullOnDelete();
            $table->unsignedBigInteger('functional_unit_id')->nullable();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->foreignId('communication_purpose_id')->constrained('communication_purposes')->cascadeOnDelete();
            $table->foreignId('communication_channel_id')->nullable()->constrained('communication_channels')->nullOnDelete();
            $table->string('recipient_type')->nullable();
            $table->string('direction')->default('outbound')->index();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('sensitivity_level')->nullable();
            $table->foreignId('communication_profile_id')->constrained('communication_profiles')->cascadeOnDelete();
            $table->foreignId('primary_endpoint_id')->nullable()->constrained('communication_endpoints')->nullOnDelete();
            $table->foreignId('reply_to_endpoint_id')->nullable()->constrained('communication_endpoints')->nullOnDelete();
            $table->foreignId('fallback_policy_id')->nullable()->constrained('communication_fallback_policies')->nullOnDelete();
            $table->foreignId('template_group_id')->nullable()->constrained('communication_template_groups')->nullOnDelete();
            $table->foreignId('working_hours_id')->nullable()->constrained('communication_working_hours')->nullOnDelete();
            $table->foreignId('rate_limit_policy_id')->nullable()->constrained('communication_rate_limit_policies')->nullOnDelete();
            $table->unsignedInteger('priority')->default(100);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'communication_purpose_id', 'communication_channel_id', 'priority']);
        });

        Schema::create('communication_authorisations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('communication_profile_id')->nullable()->constrained('communication_profiles')->nullOnDelete();
            $table->foreignId('communication_endpoint_id')->nullable()->constrained('communication_endpoints')->nullOnDelete();
            $table->unsignedBigInteger('role_assignment_id')->nullable();
            $table->unsignedBigInteger('responsibility_assignment_id')->nullable();
            $table->unsignedBigInteger('functional_unit_membership_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('allowed_purpose_ids_json')->nullable();
            $table->json('allowed_channel_ids_json')->nullable();
            $table->json('allowed_recipient_types_json')->nullable();
            $table->boolean('can_prepare')->default(false);
            $table->boolean('can_preview')->default(false);
            $table->boolean('can_send')->default(false);
            $table->boolean('can_schedule')->default(false);
            $table->boolean('can_approve')->default(false);
            $table->boolean('can_cancel')->default(false);
            $table->boolean('can_reply')->default(false);
            $table->boolean('can_view_delivery_logs')->default(false);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'communication_profile_id', 'user_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('communication_rate_limit_policies');
        Schema::dropIfExists('communication_working_hours');
        Schema::dropIfExists('communication_authorisations');
        Schema::dropIfExists('communication_routing_rules');
        Schema::dropIfExists('communication_profile_endpoints');
        Schema::dropIfExists('communication_profiles');
        Schema::dropIfExists('communication_endpoints');
        Schema::dropIfExists('communication_providers');
        Schema::dropIfExists('communication_fallback_steps');
        Schema::dropIfExists('communication_fallback_policies');
        Schema::dropIfExists('communication_purposes');
        Schema::dropIfExists('communication_purpose_categories');
        Schema::dropIfExists('communication_endpoint_types');
        Schema::dropIfExists('communication_provider_types');
        Schema::dropIfExists('communication_channels');
    }
};
