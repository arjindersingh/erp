<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->nullable()->unique();
            $table->string('status')->default('active')->index();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('code')->nullable();
            $table->string('type')->default('company')->index();
            $table->string('registration_number')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->unique(['tenant_id', 'code']);
            $table->unique(['tenant_id', 'id']);
        });

        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('code')->nullable();
            $table->string('status')->default('active')->index();
            $table->string('address_line_one')->nullable();
            $table->string('address_line_two')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'slug']);
            $table->unique(['company_id', 'code']);
            $table->index(['tenant_id', 'company_id']);
            $table->unique(['tenant_id', 'company_id', 'id']);
        });

        Schema::create('institute_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('institutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institute_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('code')->nullable();
            $table->string('affiliation_number')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['campus_id', 'slug']);
            $table->unique(['campus_id', 'code']);
            $table->index(['tenant_id', 'company_id', 'campus_id']);
            $table->unique(['tenant_id', 'company_id', 'campus_id', 'id']);
        });

        Schema::create('tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false)->index();
            $table->boolean('is_verified')->default(false)->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('access_scopes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('parent_scope_id')->nullable();
            $table->string('scope_type')->index();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->unsignedBigInteger('institute_id')->nullable();
            $table->string('name');
            $table->string('code');
            $table->unsignedTinyInteger('level')->index();
            $table->string('path')->index();
            $table->string('status')->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'id']);
            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'scope_type', 'status']);
            $table->foreign(['tenant_id', 'parent_scope_id'], 'access_scopes_parent_fk')
                ->references(['tenant_id', 'id'])->on('access_scopes')->restrictOnDelete();
            $table->foreign(['tenant_id', 'company_id'], 'access_scopes_company_fk')
                ->references(['tenant_id', 'id'])->on('companies')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'company_id', 'campus_id'], 'access_scopes_campus_fk')
                ->references(['tenant_id', 'company_id', 'id'])->on('campuses')->cascadeOnDelete();
            $table->foreign(
                ['tenant_id', 'company_id', 'campus_id', 'institute_id'],
                'access_scopes_institute_fk'
            )->references(['tenant_id', 'company_id', 'campus_id', 'id'])
                ->on('institutes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_scopes');
        Schema::dropIfExists('tenant_domains');
        Schema::dropIfExists('institutes');
        Schema::dropIfExists('institute_types');
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('tenants');
    }
};
