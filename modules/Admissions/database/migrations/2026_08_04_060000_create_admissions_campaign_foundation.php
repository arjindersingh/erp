<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('institute_id');
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('application_opens_at');
            $table->timestamp('application_closes_at');
            $table->timestamp('submission_deadline_at');
            $table->timestamp('scrutiny_starts_at')->nullable();
            $table->timestamp('scrutiny_ends_at')->nullable();
            $table->timestamp('selection_starts_at')->nullable();
            $table->timestamp('selection_ends_at')->nullable();
            $table->string('application_number_series_code')->nullable();
            $table->json('settings')->nullable();
            $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'id']);
            $table->unique(['tenant_id', 'institute_id', 'academic_year_id', 'code'], 'admission_campaign_context_code_unique');
            $table->index(['tenant_id', 'status', 'application_opens_at', 'application_closes_at'], 'admission_campaign_public_lookup');
            $table->foreign(['tenant_id', 'company_id'], 'admission_campaign_company_fk')->references(['tenant_id', 'id'])->on('companies')->restrictOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id'], 'admission_campaign_campus_fk')->references(['tenant_id', 'company_id', 'id'])->on('campuses')->restrictOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'admission_campaign_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->restrictOnDelete();
        });

        Schema::create('admission_campaign_offerings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('campaign_id');
            $table->string('offering_type');
            $table->foreignId('academic_class_id')->nullable()->constrained('academic_classes')->restrictOnDelete();
            $table->foreignId('programme_offering_id')->nullable()->constrained('programme_offerings')->restrictOnDelete();
            $table->unsignedSmallInteger('preference_order')->default(1);
            $table->unsignedInteger('intake_capacity')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'id']);
            $table->unique(['campaign_id', 'academic_class_id'], 'admission_campaign_class_unique');
            $table->unique(['campaign_id', 'programme_offering_id'], 'admission_campaign_programme_unique');
            $table->index(['tenant_id', 'campaign_id', 'offering_type', 'is_active'], 'admission_offering_lookup');
            $table->foreign(['tenant_id', 'campaign_id'], 'admission_offering_campaign_fk')->references(['tenant_id', 'id'])->on('admission_campaigns')->cascadeOnDelete();
        });

        Schema::create('admission_applications', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('institute_id');
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->unsignedBigInteger('campaign_id');
            $table->string('application_number')->nullable();
            $table->string('source')->index();
            $table->string('source_reference')->nullable();
            $table->json('source_metadata')->nullable();
            $table->string('applicant_given_name');
            $table->string('applicant_family_name')->nullable();
            $table->date('applicant_date_of_birth')->nullable();
            $table->string('applicant_email')->nullable();
            $table->string('applicant_mobile', 32)->nullable();
            $table->char('identity_fingerprint', 64)->nullable();
            $table->char('access_token_hash', 64)->nullable()->unique();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('submission_location')->nullable();
            $table->string('paper_form_number')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('data_entry_completed_at')->nullable();
            $table->string('assisted_entry_reason')->nullable();
            $table->string('applicant_confirmation_method')->nullable();
            $table->timestamp('applicant_confirmed_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('submission_ip', 45)->nullable();
            $table->text('submission_user_agent')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'id']);
            $table->unique(['tenant_id', 'application_number']);
            $table->unique(['tenant_id', 'campaign_id', 'paper_form_number']);
            $table->index(['tenant_id', 'campaign_id', 'status'], 'admission_application_queue_lookup');
            $table->index(['tenant_id', 'campaign_id', 'identity_fingerprint'], 'admission_application_duplicate_lookup');
            $table->foreign(['tenant_id', 'campaign_id'], 'admission_application_campaign_fk')->references(['tenant_id', 'id'])->on('admission_campaigns')->restrictOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id', 'institute_id'], 'admission_application_institute_fk')->references(['tenant_id', 'company_id', 'campus_id', 'id'])->on('institutes')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
        Schema::dropIfExists('admission_campaign_offerings');
        Schema::dropIfExists('admission_campaigns');
    }
};
