<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('delivery_routes', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_routes', 'route_date')) {
                $table->date('route_date')->after('route_name');
            }
            if (Schema::hasColumn('delivery_routes', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('delivery_routes', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
    public function down() {
        Schema::table('delivery_routes', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_routes', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('delivery_routes', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (Schema::hasColumn('delivery_routes', 'route_date')) {
                $table->dropColumn('route_date');
            }
        });
    }
};
