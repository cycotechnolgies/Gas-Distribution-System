<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assistants = Assistant::orderBy('name')->paginate(12);
        return view('assistants.index', compact('assistants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assistants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:191',
            'phone'  => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'notes'  => 'nullable|string',
        ]);

        Assistant::create($data);

        return redirect()->route('assistants.index')->with('success', 'Assistant created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assistant $assistant)
    {
        return view('assistants.edit', compact('assistant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assistant $assistant)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:191',
            'phone'  => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'notes'  => 'nullable|string',
        ]);

        $assistant->update($data);

        return redirect()->route('assistants.index')->with('success', 'Assistant updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assistant $assistant)
    {
        // Optional: prevent delete if assigned to routes
        if (method_exists($assistant, 'deliveryRoutes') && $assistant->deliveryRoutes()->count() > 0) {
            return redirect()->route('assistants.index')->with('error', 'Cannot delete assistant assigned to delivery routes.');
        }

        $assistant->delete();

        return redirect()->route('assistants.index')->with('success', 'Assistant deleted.');
    }
}
