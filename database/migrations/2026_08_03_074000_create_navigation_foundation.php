<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->string('short_name')->nullable()->after('name');
            $table->string('theme_key')->nullable()->after('icon');
            $table->string('default_route_name')->nullable()->after('route_prefix');
            $table->string('module_type')->default('administrative')->index();
            $table->boolean('supports_academic_year')->default(false);
            $table->boolean('supports_company_scope')->default(true);
            $table->boolean('supports_campus_scope')->default(true);
            $table->boolean('supports_institute_scope')->default(true);
        });

        Schema::create('module_feature_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['module_id', 'code']);
        });

        Schema::table('module_features', function (Blueprint $table) {
            $table->foreignId('feature_group_id')->nullable()->after('module_id')->constrained('module_feature_groups')->nullOnDelete();
            $table->string('short_name')->nullable()->after('name');
            $table->string('route_name')->nullable();
            $table->string('icon')->nullable();
            $table->string('feature_type')->default('resource')->index();
            $table->boolean('supports_search')->default(true);
            $table->boolean('supports_favourites')->default(true);
            $table->boolean('supports_quick_action')->default(false);
        });

        Schema::create('tenant_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_enabled')->default(false)->index();
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->json('configuration_json')->nullable();
            $table->string('display_name_override')->nullable();
            $table->string('icon_override')->nullable();
            $table->unsignedInteger('display_order_override')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'module_id']);
            $table->index(['tenant_id', 'is_enabled', 'starts_at', 'ends_at'], 'tenant_modules_effective_index');
        });

        Schema::create('portals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('default_route_name')->nullable();
            $table->boolean('requires_authentication')->default(true)->index();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('menu_sets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->string('menu_type')->default('sidebar')->index();
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_system')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'portal_id', 'code', 'menu_type'], 'menu_sets_boundary_unique');
            $table->index(['portal_id', 'tenant_id', 'status', 'is_default'], 'menu_sets_resolution_index');
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('menu_set_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('module_feature_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('short_title')->nullable();
            $table->text('description')->nullable();
            $table->string('route_name')->nullable();
            $table->json('route_parameters_json')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('icon')->nullable();
            $table->string('badge_type')->nullable();
            $table->string('badge_source')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->unsignedTinyInteger('depth')->default(0)->index();
            $table->string('item_type')->default('link')->index();
            $table->string('target')->default('same_window');
            $table->string('permission_code')->nullable()->index();
            $table->json('requires_any_permission_json')->nullable();
            $table->json('requires_all_permissions_json')->nullable();
            $table->json('visible_when_json')->nullable();
            $table->boolean('is_collapsible')->default(false);
            $table->boolean('is_expanded_by_default')->default(false);
            $table->boolean('is_favourite_allowed')->default(true);
            $table->boolean('is_searchable')->default(true);
            $table->boolean('is_system')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['menu_set_id', 'parent_id', 'display_order'], 'menu_items_tree_index');
            $table->index(['module_id', 'module_feature_id', 'status'], 'menu_items_feature_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_sets');
        Schema::dropIfExists('portals');
        Schema::dropIfExists('tenant_modules');

        Schema::table('module_features', function (Blueprint $table) {
            $table->dropConstrainedForeignId('feature_group_id');
            $table->dropColumn(['short_name', 'route_name', 'icon', 'feature_type', 'supports_search', 'supports_favourites', 'supports_quick_action']);
        });

        Schema::dropIfExists('module_feature_groups');

        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['short_name', 'theme_key', 'default_route_name', 'module_type', 'supports_academic_year', 'supports_company_scope', 'supports_campus_scope', 'supports_institute_scope']);
        });
    }
};
