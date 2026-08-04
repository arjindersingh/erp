<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_interface_settings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('portal_id')->constrained('portals')->cascadeOnDelete();
            $table->foreignId('default_theme_preset_id')->nullable()->constrained('ui_theme_presets')->nullOnDelete();
            $table->foreignId('default_font_family_id')->nullable()->constrained('ui_font_families')->nullOnDelete();
            $table->string('default_appearance_mode')->nullable();
            $table->string('default_font_scale')->nullable();
            $table->string('default_density')->nullable();
            $table->string('default_sidebar_mode')->nullable();
            $table->string('default_navigation_style')->nullable();
            $table->string('default_content_width')->nullable();
            $table->boolean('show_global_search')->default(true);
            $table->boolean('show_breadcrumbs')->default(true);
            $table->boolean('show_notifications')->default(true);
            $table->boolean('show_context_switcher')->default(true);
            $table->boolean('show_profile_photo')->default(true);
            $table->boolean('show_footer')->default(true);
            $table->json('allowed_theme_presets_json')->nullable();
            $table->json('allowed_font_families_json')->nullable();
            $table->json('allowed_colour_palettes_json')->nullable();
            $table->json('allowed_font_scales_json')->nullable();
            $table->json('allowed_density_modes_json')->nullable();
            $table->json('allowed_sidebar_modes_json')->nullable();
            $table->json('allowed_content_widths_json')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tenant_id', 'portal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_interface_settings');
    }
};
