<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_interface_settings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('brand_name');
            $table->string('short_brand_name')->nullable();
            $table->foreignId('logo_document_id')->nullable();
            $table->foreignId('compact_logo_document_id')->nullable();
            $table->foreignId('favicon_document_id')->nullable();
            $table->foreignId('login_background_document_id')->nullable();
            $table->foreignId('default_theme_preset_id')->nullable()->constrained('ui_theme_presets')->nullOnDelete();
            $table->foreignId('primary_palette_id')->nullable()->constrained('ui_colour_palettes')->nullOnDelete();
            $table->foreignId('secondary_palette_id')->nullable()->constrained('ui_colour_palettes')->nullOnDelete();
            $table->string('login_layout')->nullable();
            $table->string('header_style')->nullable();
            $table->string('sidebar_style')->nullable();
            $table->string('footer_style')->nullable();
            $table->boolean('allow_user_appearance_mode')->default(true);
            $table->boolean('allow_user_theme_selection')->default(true);
            $table->boolean('allow_user_font_selection')->default(true);
            $table->boolean('allow_user_font_scale')->default(true);
            $table->boolean('allow_user_palette_selection')->default(true);
            $table->boolean('allow_user_density_selection')->default(true);
            $table->boolean('allow_user_sidebar_selection')->default(true);
            $table->boolean('allow_user_content_width')->default(true);
            $table->boolean('allow_user_table_preferences')->default(true);
            $table->boolean('allow_user_dashboard_preferences')->default(true);
            $table->boolean('allow_user_accessibility_preferences')->default(true);
            $table->string('minimum_font_scale')->nullable();
            $table->string('maximum_font_scale')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_interface_settings');
    }
};
