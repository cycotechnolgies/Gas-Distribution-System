<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'delivered_adjusted')) {
                $table->boolean('delivered_adjusted')->default(false)->after('delivered');
            }
        });
    }
    public function down() {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'delivered_adjusted')) {
                $table->dropColumn('delivered_adjusted');
            }
        });
    }
};
