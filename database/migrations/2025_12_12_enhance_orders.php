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
        // Enhance orders table
        Schema::table('orders', function (Blueprint $table) {
            // Add status enum if not exists
            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['Pending', 'Loaded', 'Delivered', 'Completed', 'Cancelled'])
                    ->default('Pending')
                    ->after('customer_id');
            }

            // Add urgency flag
            if (!Schema::hasColumn('orders', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('status');
            }

            // Add delivery route assignment
            if (!Schema::hasColumn('orders', 'delivery_route_id')) {
                $table->foreignId('delivery_route_id')
                    ->nullable()
                    ->constrained('delivery_routes')
                    ->nullOnDelete()
                    ->after('is_urgent');
            }

            // Add order total
            if (!Schema::hasColumn('orders', 'order_total')) {
                $table->decimal('order_total', 12, 2)->default(0)->after('delivery_route_id');
            }

            // Add notes
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('order_total');
            }

            // Add status tracking timestamps
            if (!Schema::hasColumn('orders', 'loaded_at')) {
                $table->timestamp('loaded_at')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('loaded_at');
            }

            if (!Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('delivered_at');
            }
        });

        // Create order_items table if not exists
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('gas_type_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('line_total', 12, 2);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['order_id', 'gas_type_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropTableIfExists('order_items');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['delivery_route_id']);
            $table->dropColumnIfExists([
                'status',
                'is_urgent',
                'delivery_route_id',
                'order_total',
                'notes',
                'loaded_at',
                'delivered_at',
                'completed_at'
            ]);
        });
    }
};
