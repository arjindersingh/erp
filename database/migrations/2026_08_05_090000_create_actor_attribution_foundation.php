<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_sessions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('public_access_identity_id')->nullable()->index();
            $table->string('session_identifier_hash')->index();
            $table->string('first_request_id')->nullable();
            $table->string('last_request_id')->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->string('first_route_name')->nullable();
            $table->string('last_route_name')->nullable();
            $table->string('ip_hash')->nullable();
            $table->string('user_agent_summary')->nullable();
            $table->string('verification_state')->default('pending')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('public_access_identities', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('identity_type')->index();
            $table->string('identifier_hash')->index();
            $table->string('masked_identifier')->nullable();
            $table->string('verification_method')->nullable();
            $table->string('verification_status')->default('pending')->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('public_session_id')->nullable();
            $table->string('last_request_id')->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('impersonation_sessions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('impersonator_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('impersonated_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->json('approved_scope_json')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('ended_reason')->nullable();
            $table->string('request_id')->nullable();
            $table->string('session_identifier_hash')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::table('public_sessions', function (Blueprint $table): void {
            $table->foreign('public_access_identity_id')->references('id')->on('public_access_identities')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table): void {
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
        });

        Schema::table('persons', function (Blueprint $table): void {
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
        });

        Schema::table('user_memberships', function (Blueprint $table): void {
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
        });

        Schema::table('admission_applications', function (Blueprint $table): void {
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
            $table->string('submitted_by_user_id')->nullable();
            $table->string('submitted_actor_type')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('approved_by_user_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('rejected_by_user_id')->nullable();
            $table->timestamp('rejected_at')->nullable();
        });

        Schema::table('student_profiles', function (Blueprint $table): void {
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
        });

        Schema::table('guardian_profiles', function (Blueprint $table): void {
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
        });

        Schema::table('employee_profiles', function (Blueprint $table): void {
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
        });

        Schema::table('setting_values', function (Blueprint $table): void {
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
        });

        Schema::table('communication_endpoints', function (Blueprint $table): void {
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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_sessions');
        Schema::dropIfExists('public_access_identities');
        Schema::dropIfExists('impersonation_sessions');
    }
};
