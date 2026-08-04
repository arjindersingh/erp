<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_definitions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('setting_group_id')->constrained('setting_groups')->cascadeOnDelete();
            $table->foreignId('setting_option_set_id')->nullable()->constrained('setting_option_sets')->nullOnDelete();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('help_text')->nullable();
            $table->string('value_type')->default('string');
            $table->json('default_value_json')->nullable();
            $table->json('allowed_scopes_json')->nullable();
            $table->json('validation_rules_json')->nullable();
            $table->json('allowed_values_json')->nullable();
            $table->string('ui_component')->default('text');
            $table->string('placeholder')->nullable();
            $table->unsignedInteger('display_order')->default(100);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_secret')->default(false);
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_inheritable')->default(true);
            $table->boolean('is_cacheable')->default(true);
            $table->boolean('is_user_overridable')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->boolean('requires_restart')->default(false);
            $table->boolean('is_system')->default(true);
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_definitions');
    }
};