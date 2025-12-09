<?php

namespace App\Http\Controllers;

use App\Models\GasType;
use Illuminate\Http\Request;

class GasTypeController extends Controller
{
    public function index()
    {
        $gasTypes = GasType::latest()->paginate(12);
        return view('gas-types.index', compact('gasTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'price' => 'required|numeric|min:0'
        ]);

        GasType::create($request->all());

        return back()->with('success', 'Gas type created successfully.');
    }

    public function update(Request $request, GasType $gasType)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'price' => 'required|numeric|min:0'
        ]);

        $gasType->update($request->all());

        return back()->with('success', 'Gas type updated successfully.');
    }

    public function destroy(GasType $gasType)
    {
        $gasType->delete();

        return back()->with('success', 'Gas type deleted successfully.');
    }
}
