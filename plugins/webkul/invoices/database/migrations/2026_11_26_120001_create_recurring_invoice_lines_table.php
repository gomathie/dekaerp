<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices_recurring_template_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recurring_template_id')->constrained('invoices_recurring_templates')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products_products')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts_accounts')->nullOnDelete();
            $table->foreignId('uom_id')->nullable()->constrained('unit_of_measures')->nullOnDelete();
            $table->string('name', 255)->nullable();
            $table->decimal('quantity', 20, 6)->default(1);
            $table->decimal('price_unit', 20, 6)->default(0);
            $table->decimal('discount', 8, 4)->default(0);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('invoices_recurring_template_line_taxes', function (Blueprint $table): void {
            $table->foreignId('recurring_template_line_id')->constrained('invoices_recurring_template_lines')->cascadeOnDelete();
            $table->foreignId('tax_id')->constrained('accounts_taxes')->cascadeOnDelete();
            $table->primary(['recurring_template_line_id', 'tax_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices_recurring_template_line_taxes');
        Schema::dropIfExists('invoices_recurring_template_lines');
    }
};
