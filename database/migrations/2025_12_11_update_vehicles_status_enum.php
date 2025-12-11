<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Alter the vehicles table status enum to match the new values
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active')->change();
        });
    }

    public function down(): void
    {
        // Revert to old enum values
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('status', ['available', 'maintenance', 'on_route'])->default('available')->change();
        });
    }
};
