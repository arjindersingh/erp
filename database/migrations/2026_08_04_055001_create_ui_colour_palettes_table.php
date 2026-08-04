<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ui_colour_palettes', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('shade_50')->nullable();
            $table->string('shade_100')->nullable();
            $table->string('shade_200')->nullable();
            $table->string('shade_300')->nullable();
            $table->string('shade_400')->nullable();
            $table->string('shade_500')->nullable();
            $table->string('shade_600')->nullable();
            $table->string('shade_700')->nullable();
            $table->string('shade_800')->nullable();
            $table->string('shade_900')->nullable();
            $table->string('shade_950')->nullable();
            $table->string('contrast_text_light')->nullable();
            $table->string('contrast_text_dark')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_colour_palettes');
    }
};
