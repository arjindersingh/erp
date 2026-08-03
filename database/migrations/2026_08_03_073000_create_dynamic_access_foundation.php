<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('route_prefix')->nullable();
                $table->unsignedInteger('display_order')->default(0)->index();
                $table->boolean('is_core')->default(false)->index();
                $table->boolean('is_public')->default(false)->index();
                $table->string('status')->default('active')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('module_features')) {
            Schema::create('module_features', function (Blueprint $table) {
                $table->id();
                $table->foreignId('module_id')->constrained()->cascadeOnDelete();
                $table->uuid('uuid')->unique();
                $table->string('code');
                $table->string('name');
                $table->text('description')->nullable();
                $table->unsignedInteger('display_order')->default(0)->index();
                $table->string('status')->default('active')->index();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['module_id', 'code']);
            });
        }

        if (! Schema::hasColumn('permissions', 'uuid')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique();
                $table->foreignId('module_id')->nullable()->after('uuid')->constrained()->restrictOnDelete();
                $table->foreignId('module_feature_id')->nullable()->after('module_id')->constrained()->nullOnDelete();
                $table->string('code')->nullable()->after('name');
                $table->string('command')->nullable()->after('guard_name');
                $table->text('description')->nullable();
                $table->string('permission_type')->default('command')->index();
                $table->boolean('is_system')->default(true)->index();
                $table->string('status')->default('active')->index();
                $table->softDeletes();
                $table->unique(['code', 'guard_name']);
                $table->index(['module_id', 'module_feature_id', 'status'], 'permissions_catalogue_index');
            });
        }

        if (! Schema::hasColumn('roles', 'uuid')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique(['name', 'guard_name']);
                $table->uuid('uuid')->nullable()->unique();
                $table->foreignId('tenant_id')->nullable()->after('uuid')->constrained()->cascadeOnDelete();
                $table->string('code')->nullable()->after('name');
                $table->text('description')->nullable();
                $table->string('role_type')->default('staff')->index();
                $table->boolean('is_system')->default(false)->index();
                $table->boolean('is_editable')->default(true);
                $table->boolean('is_assignable')->default(true)->index();
                $table->string('status')->default('active')->index();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->softDeletes();
                $table->unique(['tenant_id', 'code', 'guard_name']);
                $table->index(['tenant_id', 'status', 'is_assignable'], 'roles_assignment_index');
            });
        }

        $existingGrants = collect();
        foreach (['role_permissions', 'role_has_permissions'] as $legacyTable) {
            if (Schema::hasTable($legacyTable)) {
                $existingGrants = $existingGrants->merge(
                    DB::table($legacyTable)->get(['role_id', 'permission_id'])
                );
                Schema::drop($legacyTable);
            }
        }

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
            $table->index(['tenant_id', 'role_id', 'status'], 'role_permissions_effective_index');
        });

        foreach ($existingGrants->unique(fn (object $grant): string => $grant->role_id.':'.$grant->permission_id) as $grant) {
            DB::table('role_permissions')->insert([
                'role_id' => $grant->role_id,
                'permission_id' => $grant->permission_id,
                'granted_at' => now(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');

        Schema::table('roles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn(['uuid', 'code', 'description', 'role_type', 'is_system', 'is_editable', 'is_assignable', 'status', 'deleted_at']);
            $table->unique(['name', 'guard_name']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('module_feature_id');
            $table->dropConstrainedForeignId('module_id');
            $table->dropColumn(['uuid', 'code', 'command', 'description', 'permission_type', 'is_system', 'status', 'deleted_at']);
        });

        Schema::dropIfExists('module_features');
        Schema::dropIfExists('modules');

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });
    }
};
