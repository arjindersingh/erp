<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('display_name');
            $table->string('gender')->nullable()->index();
            $table->date('date_of_birth')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('primary_email')->nullable()->index();
            $table->string('primary_mobile')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'id']);
            $table->index(['tenant_id', 'last_name', 'first_name']);
        });

        Schema::create('person_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('person_id');
            $table->string('type')->index();
            $table->string('label')->nullable();
            $table->string('value');
            $table->string('normalized_value');
            $table->boolean('is_primary')->default(false)->index();
            $table->boolean('is_verified')->default(false)->index();
            $table->timestamp('verified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['person_id', 'type', 'normalized_value']);
            $table->index(['tenant_id', 'type', 'normalized_value']);
            $table->foreign(['tenant_id', 'person_id'], 'person_contacts_person_fk')
                ->references(['tenant_id', 'id'])->on('persons')->cascadeOnDelete();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('person_id');
            $table->string('type')->index();
            $table->string('display_name')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('status')->default('active')->index();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'person_id', 'type']);
            $table->unique(['tenant_id', 'type', 'reference_number']);
            $table->unique(['tenant_id', 'person_id', 'id']);
            $table->index(['tenant_id', 'type', 'status']);
            $table->foreign(['tenant_id', 'person_id'], 'profiles_person_fk')
                ->references(['tenant_id', 'id'])->on('persons')->cascadeOnDelete();
        });

        Schema::create('user_person_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('person_id');
            $table->boolean('is_primary')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id']);
            $table->unique(['tenant_id', 'person_id']);
            $table->unique(['tenant_id', 'user_id', 'person_id']);
            $table->index(['user_id', 'status']);
            $table->foreign(['tenant_id', 'person_id'], 'user_person_links_person_fk')
                ->references(['tenant_id', 'id'])->on('persons')->cascadeOnDelete();
        });

        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('person_id')->nullable();
            $table->unsignedBigInteger('profile_id')->nullable();
            $table->unsignedBigInteger('access_scope_id');
            $table->string('membership_type')->index();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->string('active_identity_key', 64)->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['tenant_id', 'user_id', 'access_scope_id', 'membership_type', 'profile_id'],
                'memberships_identity_scope_index'
            );
            $table->unique(['tenant_id', 'id', 'user_id']);
            $table->index(['tenant_id', 'user_id', 'status']);
            $table->index(['tenant_id', 'person_id', 'status']);
            $table->foreign(
                ['tenant_id', 'user_id', 'person_id'],
                'memberships_user_person_fk'
            )->references(['tenant_id', 'user_id', 'person_id'])
                ->on('user_person_links')->cascadeOnDelete();
            $table->foreign(
                ['tenant_id', 'person_id', 'profile_id'],
                'memberships_profile_fk'
            )->references(['tenant_id', 'person_id', 'id'])
                ->on('profiles')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'access_scope_id'], 'memberships_scope_fk')
                ->references(['tenant_id', 'id'])->on('access_scopes')->cascadeOnDelete();
        });

        Schema::create('role_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_membership_id');
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->unsignedBigInteger('access_scope_id');
            $table->boolean('is_primary')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('active_identity_key', 64)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_membership_id', 'role_id', 'access_scope_id'], 'role_assignment_lookup_index');
            $table->index(['tenant_id', 'user_id', 'status']);
            $table->foreign(
                ['tenant_id', 'user_membership_id', 'user_id'],
                'role_assignments_membership_fk'
            )->references(['tenant_id', 'id', 'user_id'])
                ->on('user_memberships')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'access_scope_id'], 'role_assignments_scope_fk')
                ->references(['tenant_id', 'id'])->on('access_scopes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_assignments');
        Schema::dropIfExists('user_memberships');
        Schema::dropIfExists('user_person_links');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('person_contacts');
        Schema::dropIfExists('persons');
    }
};
