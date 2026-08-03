<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('legal_name')->nullable()->after('name');
            $table->string('timezone')->default('UTC');
            $table->string('locale', 10)->default('en');
            $table->string('currency', 3)->default('INR');
            $table->json('branding')->nullable();
            $table->index(['status', 'deleted_at'], 'tenants_availability_index');
        });

        DB::table('tenants')->whereNull('uuid')->orderBy('id')->eachById(function (object $tenant): void {
            DB::table('tenants')->where('id', $tenant->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::table('tenant_domains', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('domain_type')->default('custom')->index();
            $table->string('status')->default('active')->index();
            $table->index(['status', 'is_verified', 'domain'], 'tenant_domains_resolution_index');
        });

        DB::table('tenant_domains')->whereNull('uuid')->orderBy('id')->eachById(function (object $domain): void {
            DB::table('tenant_domains')->where('id', $domain->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('plan_code')->index();
            $table->string('status')->default('trial')->index();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('trial_ends_at')->nullable()->index();
            $table->timestamp('renews_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->json('limits')->nullable();
            $table->json('features')->nullable();
            $table->string('external_reference')->nullable()->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'starts_at', 'ends_at'], 'tenant_subscriptions_effective_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
        Schema::table('tenant_domains', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'domain_type', 'status']);
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'legal_name', 'timezone', 'locale', 'currency', 'branding']);
        });
    }
};
