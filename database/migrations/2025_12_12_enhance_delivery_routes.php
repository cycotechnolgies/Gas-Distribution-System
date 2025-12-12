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
        // Enhance delivery_routes table
        Schema::table('delivery_routes', function (Blueprint $table) {
            // Add status tracking
            if (!Schema::hasColumn('delivery_routes', 'route_status')) {
                $table->enum('route_status', ['Planned', 'InProgress', 'Completed', 'Cancelled'])->default('Planned')->after('driver_id');
            }

            // Add actual timing
            if (!Schema::hasColumn('delivery_routes', 'actual_start_time')) {
                $table->timestamp('actual_start_time')->nullable()->after('route_status');
            }
            if (!Schema::hasColumn('delivery_routes', 'actual_end_time')) {
                $table->timestamp('actual_end_time')->nullable()->after('actual_start_time');
            }

            // Add notes
            if (!Schema::hasColumn('delivery_routes', 'notes')) {
                $table->text('notes')->nullable()->after('actual_end_time');
            }
        });

        // Create route_stops table for individual customer stops
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_route_id')->constrained('delivery_routes')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('restrict');
            
            // Stop sequencing
            $table->integer('stop_order')->comment('Order within route: 1, 2, 3...');
            
            // Planned vs actual timing
            $table->time('planned_time')->nullable()->comment('Planned delivery time for this stop');
            $table->timestamp('actual_time')->nullable()->comment('Actual delivery time when driver confirmed');
            
            // Notes
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index('delivery_route_id');
            $table->index('customer_id');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop route_stops table
        Schema::dropIfExists('route_stops');

        // Remove columns from delivery_routes
        Schema::table('delivery_routes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_routes', 'route_status')) {
                $table->dropColumn('route_status');
            }
            if (Schema::hasColumn('delivery_routes', 'actual_start_time')) {
                $table->dropColumn('actual_start_time');
            }
            if (Schema::hasColumn('delivery_routes', 'actual_end_time')) {
                $table->dropColumn('actual_end_time');
            }
            if (Schema::hasColumn('delivery_routes', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
