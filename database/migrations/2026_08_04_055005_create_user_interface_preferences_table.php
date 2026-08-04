<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_interface_preferences', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('portal_id')->constrained('portals')->cascadeOnDelete();
            $table->foreignId('theme_preset_id')->nullable()->constrained('ui_theme_presets')->nullOnDelete();
            $table->string('appearance_mode')->nullable();
            $table->foreignId('font_family_id')->nullable()->constrained('ui_font_families')->nullOnDelete();
            $table->string('font_scale')->nullable();
            $table->string('line_height')->nullable();
            $table->foreignId('primary_palette_id')->nullable()->constrained('ui_colour_palettes')->nullOnDelete();
            $table->string('interface_density')->nullable();
            $table->string('sidebar_mode')->nullable();
            $table->string('navigation_style')->nullable();
            $table->string('content_width')->nullable();
            $table->string('card_radius')->nullable();
            $table->string('table_density')->nullable();
            $table->unsignedInteger('default_rows_per_page')->nullable();
            $table->boolean('sticky_table_header')->default(false);
            $table->boolean('striped_table_rows')->default(false);
            $table->boolean('wrap_table_text')->default(true);
            $table->boolean('remember_filters')->default(false);
            $table->boolean('remember_sorting')->default(false);
            $table->boolean('remember_visible_columns')->default(false);
            $table->boolean('high_contrast')->default(false);
            $table->boolean('reduced_motion')->default(false);
            $table->boolean('enhanced_focus')->default(false);
            $table->boolean('large_click_targets')->default(false);
            $table->boolean('underline_links')->default(false);
            $table->boolean('dyslexia_friendly_font')->default(false);
            $table->boolean('reduced_transparency')->default(false);
            $table->boolean('simplified_layout')->default(false);
            $table->json('dashboard_preferences_json')->nullable();
            $table->json('additional_preferences_json')->nullable();
            $table->unsignedInteger('preference_version')->default(1);
            $table->timestamps();
            $table->unique(['tenant_id', 'user_id', 'portal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_interface_preferences');
    }
};
