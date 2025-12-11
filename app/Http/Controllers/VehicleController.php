<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vehicles.index', [
            'vehicles' => Vehicle::with('driver')->paginate(12),
            'drivers' => Driver::orderBy('name')->get(),
            'assistants' => Assistant::orderBy('name')->get(), // if needed
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number',
            'model'          => 'nullable|string|max:191',
            'capacity'       => 'required|integer|min:0',
            'status'         => 'required|in:available,maintenance,on_route',
            'notes'          => 'nullable|string',
        ]);

        Vehicle::create($data);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            // ignore unique on current vehicle id
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number,' . $vehicle->id,
            'model'          => 'nullable|string|max:191',
            'capacity'       => 'required|integer|min:0',
            'status'         => 'required|in:available,maintenance,on_route',
            'notes'          => 'nullable|string',
        ]);

        $vehicle->update($data);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Optional: prevent delete if assigned to routes
        if (method_exists($vehicle, 'deliveryRoutes') && $vehicle->deliveryRoutes()->count() > 0) {
            return redirect()->route('vehicles.index')->with('error', 'Cannot delete vehicle assigned to delivery routes.');
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted.');
    }
}
