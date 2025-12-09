<?php

namespace App\Http\Controllers;

use App\Models\GasType;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function show(GasType $gasType)
    {
        $suppliers = Supplier::all();

        $gasType->load('suppliers');

        return view('gas-types.show', compact('gasType', 'suppliers'));
    }

    // Add / Update Supplier Rate
    public function saveSupplierRate(Request $request, GasType $gasType)
    {
        $request->validate([
            'supplier_id' => 'required',
            'rate' => 'required|numeric|min:0'
        ]);

        $gasType->suppliers()->syncWithoutDetaching([
            $request->supplier_id => ['rate' => $request->rate]
        ]);

        return back()->with('success', 'Supplier rate saved.');
    }

    // Remove Supplier Rate
    public function removeSupplier(GasType $gasType, Supplier $supplier)
    {
        $gasType->suppliers()->detach($supplier->id);

        return back()->with('success', 'Supplier removed.');
    }

    public function getSupplierRate(Request $request)
    {
        $request->validate([
            'gas_type_id' => 'required',
            'supplier_id' => 'required'
        ]);
        $rate = DB::table('gas_type_supplier')
            ->where('gas_type_id', $request->gas_type_id)
            ->where('supplier_id', $request->supplier_id)
            ->value('rate');

        return response()->json([
            'rate' => $rate ?? 0
        ]);
    }

}
