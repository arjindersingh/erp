<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_programmes', function (Blueprint $t): void {
            $this->base($t);
            $t->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $t->foreignId('education_level_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('education_authority_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('code');
            $t->string('name');
            $t->unsignedSmallInteger('duration_months');
            $t->string('credit_system')->default('none');
            $t->decimal('required_credits', 7, 2)->nullable();
            $t->unique(['tenant_id', 'institute_id', 'code']);
        });
        Schema::create('academic_courses', function (Blueprint $t): void {
            $this->base($t);
            $t->foreignId('academic_programme_id')->constrained()->cascadeOnDelete();
            $t->string('code');
            $t->string('name');
            $t->unique(['tenant_id', 'academic_programme_id', 'code']);
        });
        Schema::create('programme_offerings', function (Blueprint $t): void {
            $this->context($t);
            $t->foreignId('academic_programme_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('intake_capacity')->nullable();
            $t->string('code');
            $t->unique(['tenant_id', 'institute_id', 'academic_year_id', 'academic_programme_id']);
        });
        Schema::create('programme_course_offerings', function (Blueprint $t): void {
            $this->base($t);
            $t->foreignId('programme_offering_id')->constrained()->cascadeOnDelete();
            $t->foreignId('academic_course_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('intake_capacity')->nullable();
            $t->unique(['tenant_id', 'programme_offering_id', 'academic_course_id'], 'programme_course_unique');
        });
        Schema::create('academic_classes', function (Blueprint $t): void {
            $this->context($t);
            $t->foreignId('education_level_id')->constrained()->restrictOnDelete();
            $t->foreignId('academic_course_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('code');
            $t->string('name');
            $t->unsignedSmallInteger('sequence')->default(0);
            $t->unique(['tenant_id', 'institute_id', 'academic_year_id', 'code']);
        });
        Schema::create('academic_sections', function (Blueprint $t): void {
            $this->context($t);
            $t->foreignId('academic_class_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('programme_offering_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('type')->default('section');
            $t->string('code');
            $t->string('name');
            $t->unsignedInteger('capacity')->nullable();
            $t->unique(['tenant_id', 'institute_id', 'academic_year_id', 'type', 'code'], 'academic_section_context_unique');
        });
        Schema::create('academic_subjects', function (Blueprint $t): void {
            $this->base($t);
            $t->foreignId('education_authority_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('code');
            $t->string('name');
            $t->string('subject_type')->default('theory');
            $t->decimal('credits', 7, 2)->nullable();
            $t->decimal('maximum_marks', 7, 2)->nullable();
            $t->decimal('passing_marks', 7, 2)->nullable();
            $t->unsignedSmallInteger('theory_hours')->default(0);
            $t->unsignedSmallInteger('practical_hours')->default(0);
            $t->unique(['tenant_id', 'code']);
        });
        Schema::create('subject_groups', function (Blueprint $t): void {
            $this->base($t);
            $t->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $t->string('code');
            $t->string('name');
            $t->unsignedSmallInteger('minimum_selections')->default(0);
            $t->unsignedSmallInteger('maximum_selections')->nullable();
            $t->unique(['tenant_id', 'institute_id', 'code']);
        });
        Schema::create('subject_group_members', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('subject_group_id')->constrained()->cascadeOnDelete();
            $t->foreignId('academic_subject_id')->constrained()->restrictOnDelete();
            $t->timestamps();
            $t->unique(['subject_group_id', 'academic_subject_id']);
        });
        Schema::create('academic_terms', function (Blueprint $t): void {
            $this->context($t);
            $t->string('code');
            $t->string('name');
            $t->date('starts_on');
            $t->date('ends_on');
            $t->unique(['tenant_id', 'institute_id', 'academic_year_id', 'code']);
        });
        Schema::create('semesters', function (Blueprint $t): void {
            $this->base($t);
            $t->foreignId('academic_programme_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('number');
            $t->string('name');
            $t->unique(['academic_programme_id', 'number']);
        });
        Schema::create('semester_offerings', function (Blueprint $t): void {
            $this->context($t);
            $t->foreignId('programme_offering_id')->constrained()->cascadeOnDelete();
            $t->foreignId('semester_id')->constrained()->restrictOnDelete();
            $t->foreignId('academic_term_id')->nullable()->constrained()->nullOnDelete();
            $t->date('starts_on');
            $t->date('ends_on');
            $t->unique(['programme_offering_id', 'semester_id']);
        });
        Schema::create('academic_structure_versions', function (Blueprint $t): void {
            $this->context($t);
            $t->string('version');
            $t->string('name');
            $t->foreignId('supersedes_id')->nullable()->constrained('academic_structure_versions')->nullOnDelete();
            $t->timestamp('published_at')->nullable();
            $t->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $t->unique(['tenant_id', 'institute_id', 'academic_year_id', 'version'], 'academic_structure_version_unique');
        });
        Schema::create('class_subject_mappings', function (Blueprint $t): void {
            $this->context($t);
            $t->foreignId('academic_class_id')->constrained()->cascadeOnDelete();
            $t->foreignId('academic_subject_id')->constrained()->restrictOnDelete();
            $t->foreignId('subject_group_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('academic_term_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('academic_structure_version_id')->nullable()->constrained()->restrictOnDelete();
            $this->delivery($t);
            $t->unique(['academic_class_id', 'academic_subject_id', 'academic_term_id'], 'class_subject_unique');
        });
        Schema::create('programme_subject_mappings', function (Blueprint $t): void {
            $this->base($t);
            $t->foreignId('academic_programme_id')->constrained()->cascadeOnDelete();
            $t->foreignId('academic_course_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('semester_id')->constrained()->restrictOnDelete();
            $t->foreignId('academic_subject_id')->constrained()->restrictOnDelete();
            $t->foreignId('subject_group_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('academic_structure_version_id')->constrained()->restrictOnDelete();
            $this->delivery($t);
            $t->unique(['academic_structure_version_id', 'academic_course_id', 'semester_id', 'academic_subject_id'], 'programme_subject_unique');
        });
        Schema::create('subject_offerings', function (Blueprint $t): void {
            $this->context($t);
            $t->foreignId('academic_subject_id')->constrained()->restrictOnDelete();
            $t->foreignId('class_subject_mapping_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('programme_subject_mapping_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('academic_section_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('semester_offering_id')->nullable()->constrained()->restrictOnDelete();
            $t->foreignId('academic_term_id')->nullable()->constrained()->nullOnDelete();
            $t->string('delivery_key');
            $t->unique(['tenant_id', 'delivery_key']);
        });
        Schema::create('academic_calendars', function (Blueprint $t): void {
            $this->context($t);
            $t->foreignId('academic_term_id')->nullable()->constrained()->nullOnDelete();
            $t->string('event_type');
            $t->string('title');
            $t->dateTime('starts_at');
            $t->dateTime('ends_at');
            $t->index(['tenant_id', 'institute_id', 'academic_year_id', 'starts_at'], 'academic_calendar_lookup');
        });
    }

    public function down(): void
    {
        foreach (['academic_calendars', 'subject_offerings', 'programme_subject_mappings', 'class_subject_mappings', 'academic_structure_versions', 'semester_offerings', 'semesters', 'academic_terms', 'subject_group_members', 'subject_groups', 'academic_subjects', 'academic_sections', 'academic_classes', 'programme_course_offerings', 'programme_offerings', 'academic_courses', 'academic_programmes'] as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function base(Blueprint $t): void
    {
        $t->id();
        $t->uuid('uuid')->unique();
        $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
        $t->string('status')->default('active')->index();
        $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        $t->timestamps();
        $t->softDeletes();
        $t->index(['tenant_id', 'status']);
    }

    private function context(Blueprint $t): void
    {
        $this->base($t);
        $t->foreignId('institute_id')->constrained()->cascadeOnDelete();
        $t->foreignId('academic_year_id')->constrained()->restrictOnDelete();
        $t->index(['tenant_id', 'institute_id', 'academic_year_id'], $t->getTable().'_context_lookup');
    }

    private function delivery(Blueprint $t): void
    {
        $t->string('classification')->default('core');
        $t->decimal('credits', 7, 2)->nullable();
        $t->decimal('maximum_marks', 7, 2)->nullable();
        $t->decimal('passing_marks', 7, 2)->nullable();
        $t->unsignedSmallInteger('weekly_periods')->default(0);
        $t->unsignedSmallInteger('theory_hours')->default(0);
        $t->unsignedSmallInteger('practical_hours')->default(0);
    }
};
