<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to grns table
        Schema::table('grns', function (Blueprint $table) {
            if (!Schema::hasColumn('grns', 'variance_notes')) {
                $table->text('variance_notes')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('grns', 'rejection_notes')) {
                $table->text('rejection_notes')->nullable()->after('variance_notes');
            }
        });

        // Add columns to grn_items table
        Schema::table('grn_items', function (Blueprint $table) {
            if (!Schema::hasColumn('grn_items', 'rejected_qty')) {
                $table->integer('rejected_qty')->default(0)->after('damaged_qty');
            }
            if (!Schema::hasColumn('grn_items', 'rejection_notes')) {
                $table->text('rejection_notes')->nullable()->after('rejected_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('grns', function (Blueprint $table) {
            $table->dropColumn(['variance_notes', 'rejection_notes']);
        });

        Schema::table('grn_items', function (Blueprint $table) {
            $table->dropColumn(['rejected_qty', 'rejection_notes']);
        });
    }
};
