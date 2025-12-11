<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers with statistics
     */
    public function index()
    {
        $drivers = Driver::orderBy('name')->paginate(12);
        
        // Add current route and metrics to each driver
        foreach ($drivers as $driver) {
            $driver->currentRoute = $driver->getAssignedRoute();
            $driver->metrics = $driver->getPerformanceMetrics();
        }

        $stats = Driver::getStatistics();

        return view('drivers.index', compact('drivers', 'stats'));
    }

    /**
     * Show the form for creating a new driver
     */
    public function create()
    {
        return view('drivers.create');
    }

    /**
     * Store a newly created driver in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:100|unique:drivers',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        Driver::create($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified driver with detailed information
     */
    public function show(Driver $driver)
    {
        $driver->load('deliveryRoutes');
        $metrics = $driver->getPerformanceMetrics();
        $currentRoute = $driver->getAssignedRoute();
        $completedRoutes = $driver->getCompletedRoutes()->count();
        $experienceYears = $driver->getExperienceYears();
        $performanceRating = $driver->getPerformanceRating();

        return view('drivers.show', compact(
            'driver',
            'metrics',
            'currentRoute',
            'completedRoutes',
            'experienceYears',
            'performanceRating'
        ));
    }

    /**
     * Show the form for editing the specified driver
     */
    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    /**
     * Update the specified driver in database
     */
    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:100|unique:drivers,license_number,' . $driver->id,
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.show', $driver)->with('success', 'Driver updated successfully.');
    }

    /**
     * Delete the specified driver with validation
     */
    public function destroy(Driver $driver)
    {
        if (!$driver->canBeDeleted()) {
            return back()->with('error', 'Cannot delete driver with active or in-progress routes.');
        }

        $name = $driver->name;
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', "Driver '{$name}' deleted successfully.");
    }

    /**
     * Toggle driver status
     */
    public function toggleStatus(Driver $driver)
    {
        $driver->toggleStatus();
        $status = $driver->status;

        return back()->with('success', "Driver marked as {$status}.");
    }

    /**
     * Display driver performance report
     */
    public function performanceReport(Driver $driver)
    {
        $driver->load('deliveryRoutes');
        $metrics = $driver->getPerformanceMetrics();
        $completedRoutes = $driver->getCompletedRoutes();
        $performanceRating = $driver->getPerformanceRating();

        return view('drivers.performance', compact(
            'driver',
            'metrics',
            'completedRoutes',
            'performanceRating'
        ));
    }

    /**
     * API endpoint: Get driver availability
     */
    public function getAvailability(Request $request)
    {
        $date = $request->query('date');
        $available = Driver::getAvailableForDate($date);

        return response()->json([
            'available_drivers' => $available->map(fn($d) => ['id' => $d->id, 'name' => $d->name]),
            'count' => $available->count(),
        ]);
    }
}
