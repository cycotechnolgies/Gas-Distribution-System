<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_ref')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->decimal('po_amount', 10, 2);
            $table->decimal('payment_amount', 10, 2);
            $table->enum('payment_mode', ['Cheque', 'Bank Transfer', 'Cash', 'Online'])->default('Cheque');
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->date('payment_date');
            $table->enum('status', ['Pending', 'Cleared', 'Bounced'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};