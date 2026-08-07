<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layout_presets', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sidebar_position')->nullable();
            $table->string('sidebar_state')->nullable();
            $table->string('sidebar_width')->nullable();
            $table->string('header_mode')->nullable();
            $table->string('topbar_mode')->nullable();
            $table->string('content_width')->nullable();
            $table->string('footer_mode')->nullable();
            $table->string('density')->nullable();
            $table->json('configuration_json')->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('tenant_layout_settings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('default_layout_preset_id')->nullable()->constrained('layout_presets')->nullOnDelete();
            $table->boolean('allow_user_sidebar_position')->default(true);
            $table->boolean('allow_user_sidebar_state')->default(true);
            $table->boolean('allow_user_header_mode')->default(true);
            $table->boolean('allow_user_topbar_clock')->default(true);
            $table->boolean('allow_user_content_width')->default(true);
            $table->boolean('allow_user_theme')->default(true);
            $table->boolean('allow_user_font')->default(true);
            $table->boolean('allow_user_density')->default(true);
            $table->boolean('allow_user_footer_mode')->default(true);
            $table->json('mandatory_header_context')->nullable();
            $table->json('mandatory_footer')->nullable();
            $table->json('mandatory_branding')->nullable();
            $table->json('configuration_json')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tenant_id']);
        });

        Schema::create('institute_layout_settings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained('institutes')->cascadeOnDelete();
            $table->foreignId('layout_preset_id')->nullable()->constrained('layout_presets')->nullOnDelete();
            $table->string('header_title')->nullable();
            $table->string('header_subtitle')->nullable();
            $table->boolean('show_institute_logo')->default(true);
            $table->boolean('show_academic_year')->default(true);
            $table->boolean('show_campus_name')->default(true);
            $table->json('configuration_json')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->unique(['tenant_id', 'institute_id']);
        });

        Schema::create('portal_layout_settings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('portal_id')->constrained('portals')->cascadeOnDelete();
            $table->foreignId('layout_preset_id')->nullable()->constrained('layout_presets')->nullOnDelete();
            $table->string('sidebar_position')->nullable();
            $table->string('sidebar_state')->nullable();
            $table->string('header_mode')->nullable();
            $table->string('topbar_mode')->nullable();
            $table->string('content_width')->nullable();
            $table->string('footer_mode')->nullable();
            $table->json('configuration_json')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->unique(['tenant_id', 'portal_id']);
        });

        Schema::create('module_layout_settings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('portal_id')->nullable()->constrained('portals')->nullOnDelete();
            $table->foreignId('layout_preset_id')->nullable()->constrained('layout_presets')->nullOnDelete();
            $table->string('preferred_sidebar_state')->nullable();
            $table->string('preferred_content_width')->nullable();
            $table->boolean('show_module_quick_actions')->default(true);
            $table->boolean('show_module_search')->default(true);
            $table->boolean('show_module_dashboard_widgets')->default(true);
            $table->json('configuration_json')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->unique(['tenant_id', 'module_id', 'portal_id']);
        });

        Schema::create('user_layout_preferences', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('portal_id')->nullable()->constrained('portals')->nullOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->foreignId('layout_preset_id')->nullable()->constrained('layout_presets')->nullOnDelete();
            $table->string('sidebar_position')->nullable();
            $table->string('sidebar_state')->nullable();
            $table->string('sidebar_width')->nullable();
            $table->boolean('sidebar_auto_collapse')->default(false);
            $table->string('header_mode')->nullable();
            $table->boolean('topbar_visible')->default(true);
            $table->string('topbar_clock_format')->nullable();
            $table->boolean('show_seconds')->default(true);
            $table->string('content_width')->nullable();
            $table->string('content_density')->nullable();
            $table->string('footer_mode')->nullable();
            $table->foreignId('theme_preset_id')->nullable()->constrained('ui_theme_presets')->nullOnDelete();
            $table->foreignId('colour_palette_id')->nullable()->constrained('ui_colour_palettes')->nullOnDelete();
            $table->foreignId('font_family_id')->nullable()->constrained('ui_font_families')->nullOnDelete();
            $table->string('font_size')->nullable();
            $table->string('line_height')->nullable();
            $table->string('border_radius')->nullable();
            $table->string('table_density')->nullable();
            $table->string('form_density')->nullable();
            $table->boolean('show_menu_icons')->default(true);
            $table->boolean('show_breadcrumbs')->default(true);
            $table->boolean('show_quick_actions')->default(true);
            $table->string('animation_level')->nullable();
            $table->boolean('reduced_motion')->default(false);
            $table->boolean('high_contrast')->default(false);
            $table->foreignId('default_portal_id')->nullable()->constrained('portals')->nullOnDelete();
            $table->foreignId('default_module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->foreignId('default_institute_id')->nullable()->constrained('institutes')->nullOnDelete();
            $table->foreignId('default_academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tenant_id', 'user_id', 'portal_id', 'module_id'], 'user_layout_preferences_unique');
        });

        Schema::create('user_sidebar_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('portal_id')->nullable()->constrained('portals')->nullOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_favourite')->default(false);
            $table->unsignedInteger('custom_order')->nullable();
            $table->boolean('is_hidden_by_user')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'portal_id', 'module_id', 'menu_item_id'], 'user_sidebar_items_unique');
        });

        Schema::create('user_recent_navigation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('portal_id')->nullable()->constrained('portals')->nullOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->string('route_name');
            $table->string('record_type')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->timestamp('visited_at')->useCurrent();
            $table->timestamps();
            $table->index(['user_id', 'tenant_id', 'portal_id', 'module_id'], 'user_recent_navigation_lookup');
            $table->index(['route_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_recent_navigation');
        Schema::dropIfExists('user_sidebar_items');
        Schema::dropIfExists('user_layout_preferences');
        Schema::dropIfExists('module_layout_settings');
        Schema::dropIfExists('portal_layout_settings');
        Schema::dropIfExists('institute_layout_settings');
        Schema::dropIfExists('tenant_layout_settings');
        Schema::dropIfExists('layout_presets');
    }
};
