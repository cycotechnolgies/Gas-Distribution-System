<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update customers table with enhanced fields
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->enum('customer_type', ['Dealer', 'Commercial', 'Individual'])->default('Individual')->after('email');
            }
            if (!Schema::hasColumn('customers', 'credit_limit')) {
                $table->decimal('credit_limit', 12, 2)->default(0)->after('customer_type');
            }
            if (!Schema::hasColumn('customers', 'outstanding_balance')) {
                $table->decimal('outstanding_balance', 12, 2)->default(0)->after('credit_limit');
            }
            if (!Schema::hasColumn('customers', 'full_cylinders_issued')) {
                $table->integer('full_cylinders_issued')->default(0)->after('outstanding_balance');
            }
            if (!Schema::hasColumn('customers', 'empty_cylinders_returned')) {
                $table->integer('empty_cylinders_returned')->default(0)->after('full_cylinders_issued');
            }
            if (!Schema::hasColumn('customers', 'status')) {
                $table->enum('status', ['Active', 'Inactive', 'Suspended'])->default('Active')->after('empty_cylinders_returned');
            }
        });

        // Create gas_type_customer_price table for custom pricing
        Schema::create('gas_type_customer_price', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gas_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('custom_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['customer_id', 'gas_type_id']);
            $table->index('customer_id');
            $table->index('gas_type_id');
        });

        // Create customer_cylinders table to track cylinder transactions
        Schema::create('customer_cylinders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gas_type_id')->constrained()->cascadeOnDelete();
            $table->enum('transaction_type', ['Issued', 'Returned']); // Issued or Returned
            $table->integer('quantity');
            $table->date('transaction_date');
            $table->string('reference')->nullable(); // Reference to Order ID or Refill ID
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('gas_type_id');
            $table->index('transaction_date');
        });

        // Create customer_pricing_tiers table for category-based pricing
        Schema::create('customer_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->enum('customer_type', ['Dealer', 'Commercial', 'Individual']);
            $table->foreignId('gas_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['customer_type', 'gas_type_id']);
            $table->index('customer_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_pricing_tiers');
        Schema::dropIfExists('customer_cylinders');
        Schema::dropIfExists('gas_type_customer_price');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'customer_type',
                'credit_limit',
                'outstanding_balance',
                'full_cylinders_issued',
                'empty_cylinders_returned',
                'status'
            ]);
        });
    }
};
