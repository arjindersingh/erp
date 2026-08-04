<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_options', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('setting_option_set_id')->constrained('setting_option_sets')->cascadeOnDelete();
            $table->string('code');
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('value_json')->nullable();
            $table->string('example')->nullable();
            $table->json('metadata_json')->nullable();
            $table->unsignedInteger('display_order')->default(100);
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_recommended')->default(false)->index();
            $table->boolean('is_system')->default(true);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['setting_option_set_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_options');
    }
};