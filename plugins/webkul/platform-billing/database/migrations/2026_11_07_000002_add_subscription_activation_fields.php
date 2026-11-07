<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

return new class extends Migration
{
    public function up(): void
    {
        // Existing rows must be checked before PostgreSQL builds the active-only index.
        if (DB::table('platform_billing_subscriptions')->where('status', 'active')
            ->select('company_id')->groupBy('company_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Resolve duplicate active subscriptions before adding the unique index.');
        }

        Schema::table('platform_billing_plans', function (Blueprint $table): void {
            $table->unsignedInteger('seat_limit')->nullable();
        });

        Schema::table('platform_billing_subscriptions', function (Blueprint $table): void {
            $table->foreignId('scheduled_price_id')->nullable()->constrained('platform_billing_prices')->restrictOnDelete();
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestampTz('overdue_at')->nullable();
            $table->unsignedInteger('seat_limit')->nullable();
        });

        DB::statement("CREATE UNIQUE INDEX platform_billing_one_active_per_company ON platform_billing_subscriptions (company_id) WHERE status = 'active'");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS platform_billing_one_active_per_company');
        Schema::table('platform_billing_subscriptions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('scheduled_price_id');
            $table->dropColumn(['cancel_at_period_end', 'overdue_at', 'seat_limit']);
        });
        Schema::table('platform_billing_plans', fn (Blueprint $table) => $table->dropColumn('seat_limit'));
    }
};
