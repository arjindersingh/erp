<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_value_histories', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('setting_value_id')->constrained('setting_values')->cascadeOnDelete();
            $table->json('old_value_json')->nullable();
            $table->json('new_value_json');
            $table->json('changed_fields_json')->nullable();
            $table->string('change_source')->default('system')->index();
            $table->text('change_reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_value_histories');
    }
};