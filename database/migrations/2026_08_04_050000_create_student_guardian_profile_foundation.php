<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category_type')->index();
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'category_type', 'status']);
        });
        Schema::create('student_statuses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active_status')->default(false);
            $table->boolean('is_terminal_status')->default(false);
            $table->boolean('allows_enrolment')->default(false);
            $table->boolean('allows_portal_access')->default(false);
            $table->boolean('allows_financial_activity')->default(false);
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'is_active_status', 'status']);
        });
        Schema::create('guardian_occupations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });
        Schema::create('guardian_relationship_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_parent_relationship')->default(false);
            $table->boolean('is_legal_relationship')->default(false);
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('person_id');
            $table->string('student_number');
            $table->string('legacy_student_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('first_admission_date')->nullable();
            $table->foreignId('student_category_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('student_status_id')->constrained()->restrictOnDelete();
            $table->string('student_type')->index();
            $table->boolean('portal_access_allowed')->default(false);
            $table->boolean('communication_allowed')->default(true);
            $table->unsignedBigInteger('photo_document_id')->nullable();
            $table->unsignedBigInteger('primary_language_id')->nullable();
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'person_id']);
            $table->unique(['tenant_id', 'student_number']);
            $table->unique(['tenant_id', 'id']);
            $table->index(['tenant_id', 'student_status_id', 'status']);
            $table->foreign(['tenant_id', 'person_id'], 'student_profiles_person_fk')->references(['tenant_id', 'id'])->on('persons')->restrictOnDelete();
        });
        Schema::create('guardian_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('person_id');
            $table->string('guardian_number')->nullable();
            $table->foreignId('occupation_id')->nullable()->constrained('guardian_occupations')->restrictOnDelete();
            $table->string('employer_name')->nullable();
            $table->string('designation')->nullable();
            $table->foreignId('education_level_id')->nullable()->constrained('education_levels')->restrictOnDelete();
            $table->unsignedBigInteger('preferred_language_id')->nullable();
            $table->boolean('portal_access_allowed')->default(false);
            $table->boolean('communication_allowed')->default(true);
            $table->boolean('financial_contact_allowed')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'person_id']);
            $table->unique(['tenant_id', 'guardian_number']);
            $table->unique(['tenant_id', 'id']);
            $table->index(['tenant_id', 'status']);
            $table->foreign(['tenant_id', 'person_id'], 'guardian_profiles_person_fk')->references(['tenant_id', 'id'])->on('persons')->restrictOnDelete();
        });
        Schema::create('student_guardian_relationships', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('student_profile_id');
            $table->unsignedBigInteger('guardian_profile_id');
            $table->foreignId('guardian_relationship_type_id')->constrained('guardian_relationship_types', 'id', 'student_guardian_relationship_type_fk')->restrictOnDelete();
            $table->boolean('is_primary_guardian')->default(false);
            $table->boolean('is_legal_guardian')->default(false);
            $table->boolean('is_financial_guardian')->default(false);
            $table->boolean('is_academic_contact')->default(false);
            $table->boolean('is_emergency_contact')->default(false);
            $table->boolean('is_pickup_authorised')->default(false);
            $table->boolean('is_medical_consent_authority')->default(false);
            $table->boolean('is_portal_contact')->default(false);
            $table->boolean('is_residential_guardian')->default(false);
            $table->unsignedSmallInteger('priority')->default(100);
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('court_order_reference')->nullable();
            $table->unsignedBigInteger('supporting_document_id')->nullable();
            $table->json('communication_preference_json')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'id']);
            $table->index(['tenant_id', 'student_profile_id', 'status', 'starts_on', 'ends_on'], 'student_guardian_student_lookup');
            $table->index(['tenant_id', 'guardian_profile_id', 'status'], 'student_guardian_guardian_lookup');
            $table->foreign(['tenant_id', 'student_profile_id'], 'student_guardian_student_fk')->references(['tenant_id', 'id'])->on('student_profiles')->restrictOnDelete();
            $table->foreign(['tenant_id', 'guardian_profile_id'], 'student_guardian_guardian_fk')->references(['tenant_id', 'id'])->on('guardian_profiles')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_guardian_relationships');
        Schema::dropIfExists('guardian_profiles');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('guardian_relationship_types');
        Schema::dropIfExists('guardian_occupations');
        Schema::dropIfExists('student_statuses');
        Schema::dropIfExists('student_categories');
    }
};
