<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    /**
     * Display a listing of assistants with statistics
     */
    public function index()
    {
        $assistants = Assistant::orderBy('name')->paginate(12);

        // Add current route and metrics to each assistant
        foreach ($assistants as $assistant) {
            $assistant->currentRoute = $assistant->getAssignedRoute();
            $assistant->metrics = $assistant->getPerformanceMetrics();
        }

        $stats = Assistant::getStatistics();

        return view('assistants.index', compact('assistants', 'stats'));
    }

    /**
     * Show the form for creating a new assistant
     */
    public function create()
    {
        return view('assistants.create');
    }

    /**
     * Store a newly created assistant in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        Assistant::create($validated);

        return redirect()->route('assistants.index')->with('success', 'Assistant created successfully.');
    }

    /**
     * Display the specified assistant with detailed information
     */
    public function show(Assistant $assistant)
    {
        $assistant->load('deliveryRoutes');
        $metrics = $assistant->getPerformanceMetrics();
        $currentRoute = $assistant->getAssignedRoute();
        $completedRoutes = $assistant->getCompletedRoutes()->count();
        $experienceYears = $assistant->getExperienceYears();

        return view('assistants.show', compact(
            'assistant',
            'metrics',
            'currentRoute',
            'completedRoutes',
            'experienceYears'
        ));
    }

    /**
     * Show the form for editing the specified assistant
     */
    public function edit(Assistant $assistant)
    {
        return view('assistants.edit', compact('assistant'));
    }

    /**
     * Update the specified assistant in database
     */
    public function update(Request $request, Assistant $assistant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $assistant->update($validated);

        return redirect()->route('assistants.show', $assistant)->with('success', 'Assistant updated successfully.');
    }

    /**
     * Delete the specified assistant with validation
     */
    public function destroy(Assistant $assistant)
    {
        if (!$assistant->canBeDeleted()) {
            return back()->with('error', 'Cannot delete assistant with active or in-progress routes.');
        }

        $name = $assistant->name;
        $assistant->delete();

        return redirect()->route('assistants.index')->with('success', "Assistant '{$name}' deleted successfully.");
    }

    /**
     * Toggle assistant status
     */
    public function toggleStatus(Assistant $assistant)
    {
        $assistant->toggleStatus();
        $status = $assistant->status;

        return back()->with('success', "Assistant marked as {$status}.");
    }
}
