<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles with statistics
     */
    public function index()
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->paginate(12);

        // Add current route and metrics to each vehicle
        foreach ($vehicles as $vehicle) {
            $vehicle->currentRoute = $vehicle->getAssignedRoute();
            $vehicle->metrics = $vehicle->getPerformanceMetrics();
            $vehicle->healthStatus = $vehicle->getHealthStatus();
        }

        $stats = Vehicle::getStatistics();
        $needsAttention = Vehicle::getNeedsAttention();
        $drivers = \App\Models\Driver::all();

        return view('vehicles.index', compact('vehicles', 'stats', 'needsAttention', 'drivers'));
    }

    /**
     * Show the form for creating a new vehicle
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created vehicle in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number',
            'model' => 'nullable|string|max:191',
            'type' => 'nullable|string|max:100',
            'capacity' => 'required|integer|min:0',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'registration_expiry' => 'nullable|date|after:today',
            'fuel_consumption' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'notes' => 'nullable|string',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    /**
     * Display the specified vehicle with detailed information
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load('deliveryRoutes');
        $metrics = $vehicle->getPerformanceMetrics();
        $currentRoute = $vehicle->getAssignedRoute();
        $completedRoutes = $vehicle->getCompletedRoutes()->count();
        $maintenanceStatus = $vehicle->getMaintenanceStatus();
        $healthStatus = $vehicle->getHealthStatus();
        $ageYears = $vehicle->getAgeYears();

        return view('vehicles.show', compact(
            'vehicle',
            'metrics',
            'currentRoute',
            'completedRoutes',
            'maintenanceStatus',
            'healthStatus',
            'ageYears'
        ));
    }

    /**
     * Show the form for editing the specified vehicle
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in database
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number,' . $vehicle->id,
            'model' => 'nullable|string|max:191',
            'type' => 'nullable|string|max:100',
            'capacity' => 'required|integer|min:0',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'registration_expiry' => 'nullable|date|after:today',
            'fuel_consumption' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Delete the specified vehicle with validation
     */
    public function destroy(Vehicle $vehicle)
    {
        if (!$vehicle->canBeDeleted()) {
            return back()->with('error', 'Cannot delete vehicle with active or in-progress routes.');
        }

        $vehicleNumber = $vehicle->vehicle_number;
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', "Vehicle '{$vehicleNumber}' deleted successfully.");
    }

    /**
     * Toggle vehicle status
     */
    public function toggleStatus(Vehicle $vehicle)
    {
        $vehicle->toggleStatus();
        $status = $vehicle->status;

        return back()->with('success', "Vehicle marked as {$status}.");
    }

    /**
     * Mark vehicle for maintenance
     */
    public function markMaintenance(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'next_due_date' => 'required|date|after:today',
        ]);

        $vehicle->markMaintenance(Carbon::parse($validated['next_due_date']));

        return back()->with('success', 'Vehicle marked for maintenance.');
    }

    /**
     * Mark vehicle as active after maintenance
     */
    public function markActiveAfterMaintenance(Vehicle $vehicle)
    {
        $vehicle->markActiveAfterMaintenance();

        return back()->with('success', 'Vehicle marked as active after maintenance.');
    }

    /**
     * Display vehicle maintenance report
     */
    public function maintenanceReport(Vehicle $vehicle)
    {
        $maintenanceStatus = $vehicle->getMaintenanceStatus();
        $completedRoutes = $vehicle->getCompletedRoutes();

        return view('vehicles.maintenance', compact('vehicle', 'maintenanceStatus', 'completedRoutes'));
    }
}
