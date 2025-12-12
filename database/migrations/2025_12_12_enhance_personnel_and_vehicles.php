<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enhance drivers table
        Schema::table('drivers', function (Blueprint $table) {
            // Add status tracking if not exists
            if (!Schema::hasColumn('drivers', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('license_number');
            }

            // Add performance metrics
            if (!Schema::hasColumn('drivers', 'total_deliveries')) {
                $table->integer('total_deliveries')->default(0)->after('status');
            }
            if (!Schema::hasColumn('drivers', 'on_time_deliveries')) {
                $table->integer('on_time_deliveries')->default(0)->after('total_deliveries');
            }
            if (!Schema::hasColumn('drivers', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->default(5.00)->after('on_time_deliveries');
            }

            // Add address and other info
            if (!Schema::hasColumn('drivers', 'address')) {
                $table->text('address')->nullable()->after('average_rating');
            }
            if (!Schema::hasColumn('drivers', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('address');
            }
        });

        // Enhance assistants table
        Schema::table('assistants', function (Blueprint $table) {
            // Add status tracking if not exists
            if (!Schema::hasColumn('assistants', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('phone');
            }

            // Add performance metrics
            if (!Schema::hasColumn('assistants', 'total_deliveries')) {
                $table->integer('total_deliveries')->default(0)->after('status');
            }
            if (!Schema::hasColumn('assistants', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->default(5.00)->after('total_deliveries');
            }

            // Add address and other info
            if (!Schema::hasColumn('assistants', 'address')) {
                $table->text('address')->nullable()->after('average_rating');
            }
            if (!Schema::hasColumn('assistants', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('address');
            }
        });

        // Enhance vehicles table
        Schema::table('vehicles', function (Blueprint $table) {
            // Add status tracking if not exists
            if (!Schema::hasColumn('vehicles', 'status')) {
                $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active')->after('capacity');
            }

            // Add delivery tracking
            if (!Schema::hasColumn('vehicles', 'total_deliveries')) {
                $table->integer('total_deliveries')->default(0)->after('status');
            }
            if (!Schema::hasColumn('vehicles', 'total_km')) {
                $table->integer('total_km')->default(0)->after('total_deliveries');
            }
            if (!Schema::hasColumn('vehicles', 'fuel_consumption')) {
                $table->decimal('fuel_consumption', 5, 2)->default(0)->comment('km per liter')->after('total_km');
            }

            // Add maintenance tracking
            if (!Schema::hasColumn('vehicles', 'last_maintenance_date')) {
                $table->date('last_maintenance_date')->nullable()->after('fuel_consumption');
            }
            if (!Schema::hasColumn('vehicles', 'next_maintenance_due')) {
                $table->date('next_maintenance_due')->nullable()->after('last_maintenance_date');
            }

            // Add registration info
            if (!Schema::hasColumn('vehicles', 'registration_expiry')) {
                $table->date('registration_expiry')->nullable()->after('next_maintenance_due');
            }
            if (!Schema::hasColumn('vehicles', 'purchase_date')) {
                $table->date('purchase_date')->nullable()->after('registration_expiry');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'total_deliveries')) {
                $table->dropColumn('total_deliveries');
            }
            if (Schema::hasColumn('drivers', 'on_time_deliveries')) {
                $table->dropColumn('on_time_deliveries');
            }
            if (Schema::hasColumn('drivers', 'average_rating')) {
                $table->dropColumn('average_rating');
            }
            if (Schema::hasColumn('drivers', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('drivers', 'hire_date')) {
                $table->dropColumn('hire_date');
            }
        });

        Schema::table('assistants', function (Blueprint $table) {
            if (Schema::hasColumn('assistants', 'total_deliveries')) {
                $table->dropColumn('total_deliveries');
            }
            if (Schema::hasColumn('assistants', 'average_rating')) {
                $table->dropColumn('average_rating');
            }
            if (Schema::hasColumn('assistants', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('assistants', 'hire_date')) {
                $table->dropColumn('hire_date');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'total_deliveries')) {
                $table->dropColumn('total_deliveries');
            }
            if (Schema::hasColumn('vehicles', 'total_km')) {
                $table->dropColumn('total_km');
            }
            if (Schema::hasColumn('vehicles', 'fuel_consumption')) {
                $table->dropColumn('fuel_consumption');
            }
            if (Schema::hasColumn('vehicles', 'last_maintenance_date')) {
                $table->dropColumn('last_maintenance_date');
            }
            if (Schema::hasColumn('vehicles', 'next_maintenance_due')) {
                $table->dropColumn('next_maintenance_due');
            }
            if (Schema::hasColumn('vehicles', 'registration_expiry')) {
                $table->dropColumn('registration_expiry');
            }
            if (Schema::hasColumn('vehicles', 'purchase_date')) {
                $table->dropColumn('purchase_date');
            }
        });
    }
};
