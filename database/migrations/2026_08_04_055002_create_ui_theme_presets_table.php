<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ui_theme_presets', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('appearance_mode')->nullable();
            $table->foreignId('primary_palette_id')->nullable()->constrained('ui_colour_palettes')->nullOnDelete();
            $table->foreignId('secondary_palette_id')->nullable()->constrained('ui_colour_palettes')->nullOnDelete();
            $table->foreignId('surface_palette_id')->nullable()->constrained('ui_colour_palettes')->nullOnDelete();
            $table->foreignId('font_family_id')->nullable()->constrained('ui_font_families')->nullOnDelete();
            $table->string('font_scale')->nullable();
            $table->string('line_height')->nullable();
            $table->string('interface_density')->nullable();
            $table->string('sidebar_mode')->nullable();
            $table->string('navigation_style')->nullable();
            $table->string('content_width')->nullable();
            $table->string('card_radius')->nullable();
            $table->json('token_overrides_json')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_theme_presets');
    }
};
