<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{

    public function index()
    {
        $drivers = Driver::orderBy('name')->paginate(12);
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        Driver::create($data);

        return redirect()->route('drivers.index')->with('success', 'Driver created.');
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $driver->update($data);

        return redirect()->route('drivers.index')->with('success', 'Driver updated.');
    }

    public function destroy(Driver $driver)
    {
        // consider preventing delete when assigned to routes
        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver deleted.');
    }
}
