<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Stock;
use App\Models\DeliveryRoute;
use App\Models\GasType;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'suppliers' => Supplier::count(),
            'customers' => Customer::count(),
            'pending_orders' => Order::where('status', 'Pending')->count(),
            'stock_items' => Stock::count(),
            'routes_today' => DeliveryRoute::whereDate('route_date', today())->count(),
        ];

        // Orders per month by status for line chart
        $statuses = Order::select('status')->distinct()->pluck('status');
        $ordersPerMonthByStatus = [];
        foreach ($statuses as $status) {
            $ordersPerMonthByStatus[$status] = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->where('status', $status)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');
        }

        // Stock per gas type for bar chart
        $stockByGasType = GasType::with('stock')->get()->mapWithKeys(function($gasType) {
            return [$gasType->name => $gasType->stock->full_qty ?? 0];
        });

        return view('dashboard.index', compact('stats', 'ordersPerMonthByStatus', 'stockByGasType', 'statuses'));
    }
}
