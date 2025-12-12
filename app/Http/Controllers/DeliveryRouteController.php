<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\RouteStop;
use App\Models\Driver;
use App\Models\Assistant;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryRouteController extends Controller
{
    /**
     * Display a listing of all delivery routes with statistics
     */
    public function index()
    {
        $routes = DeliveryRoute::with('driver', 'assistant', 'vehicle', 'stops')->paginate(10);

        // Add statistics to each route
        foreach ($routes as $route) {
            $route->stats = $route->getStatistics();
        }

        // Overall statistics
        $stats = [
            'total_routes' => DeliveryRoute::count(),
            'planned_routes' => DeliveryRoute::where('route_status', 'Planned')->count(),
            'in_progress_routes' => DeliveryRoute::where('route_status', 'InProgress')->count(),
            'completed_routes' => DeliveryRoute::where('route_status', 'Completed')->count(),
            'total_pending_stops' => RouteStop::whereNull('actual_time')->count(),
        ];

        return view('delivery-routes.index', compact('routes', 'stats'));
    }

    /**
     * Show the form for creating a new delivery route
     */
    public function create()
    {
        $drivers = Driver::where('status', 'active')->get();
        $assistants = Assistant::where('status', 'active')->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();
        $orders = Order::where('status', 'Pending')
            ->with('customer', 'items')
            ->get();

        return view('delivery-routes.create', compact('drivers', 'assistants', 'vehicles', 'customers', 'orders'));
    }

    /**
     * Store a newly created delivery route in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255',
            'route_date' => 'required|date|after_or_equal:today',
            'driver_id' => 'required|exists:drivers,id',
            'assistant_id' => 'nullable|exists:assistants,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'notes' => 'nullable|string',
            'stops' => 'required|array|min:1',
            'stops.*.customer_id' => 'required|exists:customers,id',
            'stops.*.order_id' => 'nullable|exists:orders,id',
            'stops.*.planned_time' => 'nullable|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();

            // Create the route
            $route = DeliveryRoute::create([
                'route_name' => $validated['route_name'],
                'route_date' => $validated['route_date'],
                'driver_id' => $validated['driver_id'],
                'assistant_id' => $validated['assistant_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'route_status' => 'Planned',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Add stops
            foreach ($validated['stops'] as $index => $stopData) {
                $route->addStop(
                    $stopData['customer_id'],
                    $stopData['order_id'] ?? null,
                    $stopData['planned_time'] ? date('H:i', strtotime($stopData['planned_time'])) : null,
                    $index + 1
                );
            }

            DB::commit();

            return redirect()->route('delivery-routes.show', $route)
                ->with('success', "Route '{$route->route_name}' created successfully with " . count($validated['stops']) . " stops.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create route: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified delivery route with stop details
     */
    public function show(DeliveryRoute $deliveryRoute)
    {
        $deliveryRoute->load('driver', 'assistant', 'vehicle', 'stops.customer', 'stops.order');

        $stats = $deliveryRoute->getStatistics();
        $lateCount = $deliveryRoute->getLateDeliveriesCount();
        $isFullyCompleted = $deliveryRoute->isFullyCompleted();

        return view('delivery-routes.show', compact('deliveryRoute', 'stats', 'lateCount', 'isFullyCompleted'));
    }

    /**
     * Show the form for editing the specified delivery route
     */
    public function edit(DeliveryRoute $deliveryRoute)
    {
        if (!$deliveryRoute->canBeDeleted()) {
            return redirect()->route('delivery-routes.show', $deliveryRoute)
                ->with('error', 'Cannot edit a route that is in progress or completed.');
        }

        $deliveryRoute->load('stops');
        $drivers = Driver::where('status', 'active')->get();
        $assistants = Assistant::where('status', 'active')->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();

        return view('delivery-routes.edit', compact('deliveryRoute', 'drivers', 'assistants', 'vehicles', 'customers'));
    }

    /**
     * Update the specified delivery route in database
     */
    public function update(Request $request, DeliveryRoute $deliveryRoute)
    {
        if (!$deliveryRoute->canBeDeleted()) {
            return redirect()->route('delivery-routes.show', $deliveryRoute)
                ->with('error', 'Cannot edit a route that is in progress or completed.');
        }

        $validated = $request->validate([
            'route_name' => 'required|string|max:255',
            'route_date' => 'required|date',
            'driver_id' => 'required|exists:drivers,id',
            'assistant_id' => 'nullable|exists:assistants,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'notes' => 'nullable|string',
        ]);

        $deliveryRoute->update($validated);

        return redirect()->route('delivery-routes.show', $deliveryRoute)
            ->with('success', 'Route updated successfully.');
    }

    /**
     * Delete the specified delivery route
     */
    public function destroy(DeliveryRoute $deliveryRoute)
    {
        if (!$deliveryRoute->canBeDeleted()) {
            return back()->with('error', 'Cannot delete a route that is in progress or completed.');
        }

        $routeName = $deliveryRoute->route_name;
        $deliveryRoute->delete();

        return redirect()->route('delivery-routes.index')
            ->with('success', "Route '{$routeName}' deleted successfully.");
    }

    /**
     * Add a customer stop to an existing route
     */
    public function addStop(Request $request, DeliveryRoute $deliveryRoute)
    {
        if ($deliveryRoute->route_status !== 'Planned') {
            return back()->with('error', 'Can only add stops to planned routes.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'planned_time' => 'nullable|date_format:H:i',
        ]);

        try {
            $stop = $deliveryRoute->addStop(
                $validated['customer_id'],
                $validated['order_id'] ?? null,
                $validated['planned_time'] ?? null
            );

            return back()->with('success', 'Stop added to route.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add stop: ' . $e->getMessage());
        }
    }

    /**
     * Remove a customer stop from a route
     */
    public function removeStop(DeliveryRoute $deliveryRoute, RouteStop $stop)
    {
        if ($deliveryRoute->route_status !== 'Planned') {
            return back()->with('error', 'Can only remove stops from planned routes.');
        }

        if ($stop->delivery_route_id !== $deliveryRoute->id) {
            return back()->with('error', 'Stop does not belong to this route.');
        }

        $deliveryRoute->removeStop($stop->id);

        return back()->with('success', 'Stop removed from route.');
    }

    /**
     * Assign a driver to the route
     */
    public function assignDriver(Request $request, DeliveryRoute $deliveryRoute)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $deliveryRoute->update(['driver_id' => $validated['driver_id']]);

        return back()->with('success', 'Driver assigned to route.');
    }

    /**
     * Assign an assistant to the route
     */
    public function assignAssistant(Request $request, DeliveryRoute $deliveryRoute)
    {
        $validated = $request->validate([
            'assistant_id' => 'required|exists:assistants,id',
        ]);

        $deliveryRoute->update(['assistant_id' => $validated['assistant_id']]);

        return back()->with('success', 'Assistant assigned to route.');
    }

    /**
     * Mark route as in progress
     */
    public function markInProgress(DeliveryRoute $deliveryRoute)
    {
        if (!$deliveryRoute->canTransitionTo('InProgress')) {
            return back()->with('error', 'This route cannot be marked as in progress.');
        }

        if (!$deliveryRoute->driver_id) {
            return back()->with('error', 'Route must have a driver assigned before starting.');
        }

        $deliveryRoute->markInProgress();

        return redirect()->route('delivery-routes.show', $deliveryRoute)
            ->with('success', 'Route marked as in progress.');
    }

    /**
     * Mark route as completed
     */
    public function markCompleted(DeliveryRoute $deliveryRoute)
    {
        if (!$deliveryRoute->canTransitionTo('Completed')) {
            return back()->with('error', 'This route cannot be marked as completed.');
        }

        if (!$deliveryRoute->isFullyCompleted()) {
            return back()->with('error', 'All stops must be completed before marking route as completed.');
        }

        $deliveryRoute->markCompleted();

        return redirect()->route('delivery-routes.show', $deliveryRoute)
            ->with('success', 'Route marked as completed.');
    }

    /**
     * API endpoint: Get driver availability for a date
     */
    public function getDriverAvailability(Request $request)
    {
        $date = $request->query('date');
        $routeId = $request->query('route_id');

        $query = Driver::where('status', 'active');

        if ($date) {
            // Get drivers already assigned to routes on this date
            $assignedDrivers = DeliveryRoute::where('route_date', $date)
                ->where('route_status', '!=', 'Cancelled')
                ->when($routeId, function ($q) use ($routeId) {
                    $q->where('id', '!=', $routeId);
                })
                ->pluck('driver_id')
                ->toArray();

            $query->whereNotIn('id', $assignedDrivers);
        }

        return response()->json($query->get(['id', 'name']));
    }

    /**
     * API endpoint: Get route statistics
     */
    public function getRouteStats(Request $request)
    {
        $routeId = $request->query('route_id');

        if (!$routeId) {
            return response()->json(['error' => 'Route ID required'], 400);
        }

        $route = DeliveryRoute::find($routeId);
        if (!$route) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        return response()->json([
            'stats' => $route->getStatistics(),
            'late_count' => $route->getLateDeliveriesCount(),
            'fully_completed' => $route->isFullyCompleted(),
        ]);
    }
}
