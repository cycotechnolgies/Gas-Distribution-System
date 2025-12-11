<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create refills table to track gas refills
        Schema::create('refills', function (Blueprint $table) {
            $table->id();
            $table->string('refill_ref')->unique();
            $table->foreignId('gas_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->integer('cylinders_refilled');
            $table->date('refill_date');
            $table->decimal('cost_per_cylinder', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('gas_type_id');
            $table->index('refill_date');
        });

        // Create supplier invoices table to track supplier charges
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('invoice_date');
            $table->decimal('invoice_amount', 12, 2);
            $table->enum('status', ['Pending', 'Reconciled', 'Disputed'])->default('Pending');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('purchase_order_id');
            $table->index('invoice_date');
        });

        // Add columns to purchase_orders table for tracking
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'received_count')) {
                $table->integer('received_count')->default(0)->after('status');
            }
            if (!Schema::hasColumn('purchase_orders', 'refilled_count')) {
                $table->integer('refilled_count')->default(0)->after('received_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['received_count', 'refilled_count']);
        });

        Schema::dropIfExists('supplier_invoices');
        Schema::dropIfExists('refills');
    }
};
