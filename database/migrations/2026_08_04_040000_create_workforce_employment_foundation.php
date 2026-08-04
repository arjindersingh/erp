<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designation_categories', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code'); $table->string('name'); $table->text('description')->nullable(); $table->unsignedSmallInteger('sequence')->default(0);
            $table->boolean('is_system')->default(false); $table->string('status')->default('active')->index(); $table->timestamps(); $table->softDeletes();
            $table->unique(['tenant_id', 'code']); $table->index(['tenant_id', 'status', 'sequence']);
        });
        Schema::create('designations', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designation_category_id')->constrained()->restrictOnDelete(); $table->string('code'); $table->string('name'); $table->string('short_name')->nullable(); $table->text('description')->nullable(); $table->string('grade_level')->nullable(); $table->unsignedSmallInteger('sequence')->default(0);
            $table->boolean('is_teaching_designation')->default(false); $table->boolean('is_management_designation')->default(false); $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps(); $table->softDeletes();
            $table->unique(['tenant_id', 'code']); $table->unique(['tenant_id', 'id']);
        });
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete(); $table->string('code'); $table->string('name'); $table->text('description')->nullable(); $table->boolean('is_system')->default(false); $table->string('status')->default('active')->index(); $table->timestamps(); $table->softDeletes(); $table->unique(['tenant_id', 'code']);
        });
        Schema::create('employment_types', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete(); $table->string('code'); $table->string('name'); $table->text('description')->nullable(); $table->boolean('is_system')->default(false); $table->string('status')->default('active')->index(); $table->timestamps(); $table->softDeletes(); $table->unique(['tenant_id', 'code']);
        });
        Schema::create('employment_statuses', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete(); $table->string('code'); $table->string('name'); $table->text('description')->nullable(); $table->boolean('is_active_status')->default(false); $table->boolean('is_terminal_status')->default(false); $table->boolean('is_system')->default(false); $table->string('status')->default('active')->index(); $table->timestamps(); $table->softDeletes(); $table->unique(['tenant_id', 'code']);
        });
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->unsignedBigInteger('tenant_id'); $table->unsignedBigInteger('company_id')->nullable(); $table->unsignedBigInteger('campus_id')->nullable(); $table->unsignedBigInteger('institute_id')->nullable(); $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('code'); $table->string('name'); $table->string('short_name')->nullable(); $table->string('department_type')->index(); $table->text('description')->nullable(); $table->unsignedBigInteger('head_employee_assignment_id')->nullable(); $table->unsignedSmallInteger('display_order')->default(0); $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps(); $table->softDeletes();
            $table->unique(['tenant_id', 'code']); $table->unique(['tenant_id', 'id']); $table->foreign(['tenant_id', 'parent_id'], 'departments_parent_fk')->references(['tenant_id', 'id'])->on('departments')->restrictOnDelete();
            $table->foreign(['tenant_id', 'company_id'], 'departments_company_fk')->references(['tenant_id', 'id'])->on('companies')->cascadeOnDelete(); $table->foreign(['tenant_id', 'company_id', 'campus_id'], 'departments_campus_fk')->references(['tenant_id', 'company_id', 'id'])->on('campuses')->cascadeOnDelete(); $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'departments_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->cascadeOnDelete();
        });
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->unsignedBigInteger('tenant_id'); $table->unsignedBigInteger('company_id')->nullable(); $table->unsignedBigInteger('campus_id')->nullable(); $table->unsignedBigInteger('institute_id')->nullable(); $table->unsignedBigInteger('department_id')->nullable(); $table->unsignedBigInteger('designation_id'); $table->foreignId('job_category_id')->constrained()->restrictOnDelete();
            $table->string('code'); $table->string('name'); $table->unsignedSmallInteger('sanctioned_strength')->nullable(); $table->unsignedSmallInteger('filled_strength')->nullable(); $table->string('employment_type_default')->nullable(); $table->string('status')->default('active')->index(); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps(); $table->softDeletes();
            $table->unique(['tenant_id', 'code']); $table->unique(['tenant_id', 'id']); $table->foreign(['tenant_id', 'designation_id'], 'job_posts_designation_fk')->references(['tenant_id', 'id'])->on('designations')->restrictOnDelete(); $table->foreign(['tenant_id', 'department_id'], 'job_posts_department_fk')->references(['tenant_id', 'id'])->on('departments')->restrictOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'job_posts_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->cascadeOnDelete();
        });
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->unsignedBigInteger('tenant_id'); $table->unsignedBigInteger('person_id'); $table->string('employee_number'); $table->date('joining_date')->nullable(); $table->date('confirmation_date')->nullable(); $table->date('retirement_date')->nullable(); $table->foreignId('employment_status_id')->constrained()->restrictOnDelete(); $table->unsignedBigInteger('primary_employment_assignment_id')->nullable();
            $table->string('official_email')->nullable(); $table->string('official_mobile', 30)->nullable(); $table->string('biometric_code')->nullable(); $table->date('service_start_date')->nullable(); $table->date('service_end_date')->nullable(); $table->string('status')->default('active')->index(); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps(); $table->softDeletes();
            $table->unique(['tenant_id', 'person_id']); $table->unique(['tenant_id', 'employee_number']); $table->unique(['tenant_id', 'id']); $table->foreign(['tenant_id', 'person_id'], 'employee_profiles_person_fk')->references(['tenant_id', 'id'])->on('persons')->restrictOnDelete();
        });
        Schema::create('employment_assignments', function (Blueprint $table) {
            $table->id(); $table->uuid('uuid')->unique(); $table->unsignedBigInteger('tenant_id'); $table->unsignedBigInteger('employee_profile_id'); $table->unsignedBigInteger('company_id'); $table->unsignedBigInteger('campus_id'); $table->unsignedBigInteger('institute_id'); $table->unsignedBigInteger('department_id')->nullable(); $table->unsignedBigInteger('job_post_id')->nullable(); $table->unsignedBigInteger('designation_id'); $table->foreignId('job_category_id')->constrained()->restrictOnDelete(); $table->foreignId('employment_type_id')->constrained()->restrictOnDelete(); $table->foreignId('employment_status_id')->constrained()->restrictOnDelete(); $table->unsignedBigInteger('access_scope_id'); $table->unsignedBigInteger('reports_to_assignment_id')->nullable();
            $table->date('appointment_date')->nullable(); $table->date('starts_on'); $table->date('ends_on')->nullable(); $table->date('probation_ends_on')->nullable(); $table->boolean('is_primary')->default(false)->index(); $table->boolean('is_additional_posting')->default(false); $table->decimal('workload_percentage', 5, 2)->nullable(); $table->string('appointment_order_number')->nullable(); $table->unsignedBigInteger('appointment_document_id')->nullable(); $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('approved_at')->nullable(); $table->timestamps(); $table->softDeletes();
            $table->unique(['tenant_id', 'id']); $table->index(['tenant_id', 'employee_profile_id', 'status', 'starts_on', 'ends_on'], 'employment_assignments_lookup'); $table->foreign(['tenant_id', 'employee_profile_id'], 'employment_assignments_employee_fk')->references(['tenant_id', 'id'])->on('employee_profiles')->restrictOnDelete(); $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'employment_assignments_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->restrictOnDelete(); $table->foreign(['tenant_id', 'department_id'], 'employment_assignments_department_fk')->references(['tenant_id', 'id'])->on('departments')->restrictOnDelete(); $table->foreign(['tenant_id', 'job_post_id'], 'employment_assignments_job_post_fk')->references(['tenant_id', 'id'])->on('job_posts')->restrictOnDelete(); $table->foreign(['tenant_id', 'designation_id'], 'employment_assignments_designation_fk')->references(['tenant_id', 'id'])->on('designations')->restrictOnDelete(); $table->foreign(['tenant_id', 'access_scope_id'], 'employment_assignments_scope_fk')->references(['tenant_id', 'id'])->on('access_scopes')->restrictOnDelete(); $table->foreign(['tenant_id', 'reports_to_assignment_id'], 'employment_assignments_reports_to_fk')->references(['tenant_id', 'id'])->on('employment_assignments')->restrictOnDelete();
        });
        Schema::create('employment_assignment_histories', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('tenant_id'); $table->unsignedBigInteger('employment_assignment_id'); $table->string('action')->index(); $table->json('old_values_json')->nullable(); $table->json('new_values_json')->nullable(); $table->text('reason')->nullable(); $table->date('effective_on'); $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('created_at')->useCurrent();
            $table->index(['tenant_id', 'employment_assignment_id', 'effective_on'], 'employment_history_lookup'); $table->foreign(['tenant_id', 'employment_assignment_id'], 'employment_history_assignment_fk')->references(['tenant_id', 'id'])->on('employment_assignments')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_assignment_histories'); Schema::dropIfExists('employment_assignments'); Schema::dropIfExists('employee_profiles'); Schema::dropIfExists('job_posts'); Schema::dropIfExists('departments'); Schema::dropIfExists('employment_statuses'); Schema::dropIfExists('employment_types'); Schema::dropIfExists('job_categories'); Schema::dropIfExists('designations'); Schema::dropIfExists('designation_categories');
    }
};
