<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_academic')->default(false)->index();
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('department_type_id')->nullable()->after('parent_id')->constrained()->restrictOnDelete();
        });
        Schema::table('designations', function (Blueprint $table) {
            $table->boolean('is_academic_leadership_designation')->default(false)->after('is_management_designation');
        });
        Schema::table('job_posts', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->foreignId('default_employment_type_id')->nullable()->after('filled_strength')->constrained('employment_types')->restrictOnDelete();
            $table->boolean('is_teaching_post')->default(false)->after('default_employment_type_id');
        });
        Schema::table('employment_statuses', function (Blueprint $table) {
            $table->boolean('allows_teaching_assignment')->default(false)->after('is_terminal_status');
            $table->boolean('allows_system_access')->default(false)->after('allows_teaching_assignment');
        });
        Schema::table('employment_assignments', function (Blueprint $table) {
            $table->boolean('is_acting_assignment')->default(false)->after('is_additional_posting');
        });

        $academicTypeId = DB::table('department_types')->insertGetId([
            'uuid' => (string) Str::uuid(), 'tenant_id' => null,
            'code' => 'ACADEMIC', 'name' => 'Academic Department', 'is_academic' => true,
            'is_system' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('departments')->where('department_type', 'academic')->update(['department_type_id' => $academicTypeId]);
    }

    public function down(): void
    {
        Schema::table('employment_assignments', fn (Blueprint $table) => $table->dropColumn('is_acting_assignment'));
        Schema::table('employment_statuses', fn (Blueprint $table) => $table->dropColumn(['allows_teaching_assignment', 'allows_system_access']));
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropForeign(['default_employment_type_id']);
            $table->dropColumn(['description', 'default_employment_type_id', 'is_teaching_post']);
        });
        Schema::table('designations', fn (Blueprint $table) => $table->dropColumn('is_academic_leadership_designation'));
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['department_type_id']);
            $table->dropColumn('department_type_id');
        });
        Schema::dropIfExists('department_types');
    }
};
