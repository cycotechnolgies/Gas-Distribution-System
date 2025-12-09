<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('gas_type_supplier', function (Blueprint $table) {
        $table->id();
        $table->foreignId('gas_type_id')->constrained()->cascadeOnDelete();
        $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
        $table->decimal('rate', 10, 2);
        $table->timestamps();

        $table->unique(['gas_type_id', 'supplier_id']);
    });
}


    public function down(): void
    {
        Schema::dropIfExists('gas_type_supplier');
    }
};
