<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_nomenclature_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->unsignedBigInteger('institute_id')->nullable();
            $table->string('boundary_key');
            $table->string('entity_key');
            $table->string('singular_label');
            $table->string('plural_label');
            $table->string('locale', 10)->default('en');
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['boundary_key', 'entity_key', 'locale'], 'academic_nomenclature_boundary_unique');
            $table->index(['tenant_id', 'entity_key', 'locale', 'status'], 'academic_nomenclature_lookup_index');
            $table->foreign(['tenant_id', 'company_id'], 'academic_nomenclature_company_fk')->references(['tenant_id', 'id'])->on('companies')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id'], 'academic_nomenclature_campus_fk')->references(['tenant_id', 'company_id', 'id'])->on('campuses')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'academic_nomenclature_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->cascadeOnDelete();
        });

        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ownership_key');
            $table->string('code');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            $table->string('level_category')->index();
            $table->unsignedSmallInteger('sequence')->default(0);
            $table->unsignedTinyInteger('minimum_age')->nullable();
            $table->unsignedTinyInteger('maximum_age')->nullable();
            $table->boolean('is_system')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ownership_key', 'code']);
            $table->index(['tenant_id', 'status', 'sequence']);
        });

        Schema::create('education_authorities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ownership_key');
            $table->string('authority_type')->index();
            $table->string('code');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('website')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('state_code')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_system')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ownership_key', 'code']);
            $table->index(['tenant_id', 'authority_type', 'status']);
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->unsignedBigInteger('institute_id')->nullable();
            $table->string('boundary_key');
            $table->string('code');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->date('admission_starts_on')->nullable();
            $table->date('admission_ends_on')->nullable();
            $table->boolean('is_current')->default(false)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->string('status')->default('draft')->index();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['boundary_key', 'code'], 'academic_years_boundary_code_unique');
            $table->index(['tenant_id', 'institute_id', 'status', 'starts_on', 'ends_on'], 'academic_years_resolution_index');
            $table->foreign(['tenant_id', 'company_id'], 'academic_years_company_fk')->references(['tenant_id', 'id'])->on('companies')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id'], 'academic_years_campus_fk')->references(['tenant_id', 'company_id', 'id'])->on('campuses')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'academic_years_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->cascadeOnDelete();
        });

        Schema::create('institute_authority_affiliations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('institute_id');
            $table->foreignId('education_authority_id')->constrained()->restrictOnDelete();
            $table->string('affiliation_type')->index();
            $table->string('affiliation_number')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->uuid('document_uuid')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'institute_id', 'status', 'valid_until'], 'institute_affiliations_lookup_index');
            $table->unique(['tenant_id', 'institute_id', 'education_authority_id', 'affiliation_type'], 'institute_affiliations_unique');
            $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'institute_affiliations_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_authority_affiliations');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('education_authorities');
        Schema::dropIfExists('education_levels');
        Schema::dropIfExists('academic_nomenclature_settings');
    }
};
