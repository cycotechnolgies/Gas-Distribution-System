<?php

namespace App\Http\Controllers;

use App\Models\{Refill, GasType, Supplier};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefillController extends Controller
{
    public function index()
    {
        $refills = Refill::with('gasType', 'supplier')
            ->latest()
            ->paginate(15);

        $suppliers = Supplier::all();
        $gasTypes = GasType::all();

        return view('refills.index', compact('refills', 'suppliers', 'gasTypes'));
    }

    private function generateRefillRef()
    {
        return 'RF-' . date('Ymd') . '-' . str_pad(Refill::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'gas_type_id' => 'required|exists:gas_types,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'cylinders_refilled' => 'required|integer|min:1',
            'refill_date' => 'required|date|before_or_equal:today',
            'cost_per_cylinder' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $totalCost = $request->cylinders_refilled * $request->cost_per_cylinder;

            $refill = Refill::create([
                'refill_ref' => $this->generateRefillRef(),
                'gas_type_id' => $request->gas_type_id,
                'supplier_id' => $request->supplier_id,
                'cylinders_refilled' => $request->cylinders_refilled,
                'refill_date' => $request->refill_date,
                'cost_per_cylinder' => $request->cost_per_cylinder,
                'total_cost' => $totalCost,
                'notes' => $request->notes
            ]);

            return back()->with('success', 'Refill recorded: ' . $refill->refill_ref);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Failed to record refill: ' . $e->getMessage());
        }
    }

    public function destroy(Refill $refill)
    {
        $refill->delete();
        return back()->with('success', 'Refill deleted.');
    }

    // Get refill summary by supplier
    public function supplierSummary(Supplier $supplier)
    {
        $refillsByType = $supplier->getRefillsByType();
        $totalCost = $supplier->getTotalRefillsCost();
        $totalCylinders = $supplier->getTotalCylindersRefilled();

        $recentRefills = $supplier->refills()
            ->with('gasType')
            ->latest()
            ->take(10)
            ->get();

        return view('refills.supplier-summary', compact(
            'supplier',
            'refillsByType',
            'totalCost',
            'totalCylinders',
            'recentRefills'
        ));
    }
}
