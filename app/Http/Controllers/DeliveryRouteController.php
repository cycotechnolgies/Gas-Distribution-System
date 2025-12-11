<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\Driver;
use App\Models\Assistant;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DeliveryRouteController extends Controller
{
    public function index()
    {
        return view('delivery-routes.index', [
            'routes' => DeliveryRoute::with(['driver', 'assistant', 'vehicle'])->paginate(12),
            'drivers' => Driver::orderBy('name')->get(),
            'assistants' => Assistant::orderBy('name')->get(),
            'vehicles' => Vehicle::orderBy('vehicle_number')->get(),
        ]);
    }

    public function create()
    {
        return view('delivery-routes.create', [
            'drivers' => Driver::all(),
            'assistants' => Assistant::all(),
            'vehicles' => Vehicle::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'driver_id'     => 'nullable|exists:drivers,id',
            'assistant_id'  => 'nullable|exists:assistants,id',
            'vehicle_id'    => 'nullable|exists:vehicles,id',
        ]);

        DeliveryRoute::create($validated);

        return redirect()
            ->route('delivery-routes.index')
            ->with('success', 'Delivery route created successfully.');
    }

    public function show(DeliveryRoute $route)
    {
        $route->load(['driver', 'assistant', 'vehicle']);
        return view('delivery-routes.show', compact('route'));
    }

    public function edit(DeliveryRoute $route)
    {
        return view('delivery-routes.edit', [
            'route' => $route,
            'drivers' => Driver::all(),
            'assistants' => Assistant::all(),
            'vehicles' => Vehicle::all(),
        ]);
    }

    public function update(Request $request, DeliveryRoute $route)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'driver_id'     => 'nullable|exists:drivers,id',
            'assistant_id'  => 'nullable|exists:assistants,id',
            'vehicle_id'    => 'nullable|exists:vehicles,id',
        ]);

        $route->update($validated);

        return redirect()
            ->route('delivery-routes.index')
            ->with('success', 'Route updated successfully.');
    }

    public function destroy(DeliveryRoute $route)
    {
        $route->delete();

        return redirect()
            ->route('delivery-routes.index')
            ->with('success', 'Route deleted successfully.');
    }
}
