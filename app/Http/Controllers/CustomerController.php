<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GasType;
use App\Models\CustomerPricingTier;
use App\Models\CustomerCylinder;
use App\Models\GasTypeCustomerPrice;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(12);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show form for creating new customer
     */
    public function create()
    {
        $customerTypes = ['Dealer', 'Commercial', 'Individual'];
        return view('customers.create', compact('customerTypes'));
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:customers,email',
            'address' => 'nullable|string',
            'nic' => 'nullable|string|unique:customers,nic',
            'city' => 'nullable|string|max:100',
            'customer_type' => 'required|in:Dealer,Commercial,Individual',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:Active,Inactive,Suspended'
        ]);

        $validated['outstanding_balance'] = 0;
        $validated['full_cylinders_issued'] = 0;
        $validated['empty_cylinders_returned'] = 0;
        $validated['status'] = $validated['status'] ?? 'Active';

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display customer details
     */
    public function show(Customer $customer)
    {
        $customer->load('orders', 'customPrices.gasType', 'cylinderTransactions.gasType');
        $balances = $customer->getAllCylinderBalances();
        
        return view('customers.show', compact('customer', 'balances'));
    }

    /**
     * Show form for editing customer
     */
    public function edit(Customer $customer)
    {
        $customerTypes = ['Dealer', 'Commercial', 'Individual'];
        $statuses = ['Active', 'Inactive', 'Suspended'];
        
        return view('customers.edit', compact('customer', 'customerTypes', 'statuses'));
    }

    /**
     * Update customer details
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
            'nic' => 'nullable|string|unique:customers,nic,' . $customer->id,
            'city' => 'nullable|string|max:100',
            'customer_type' => 'required|in:Dealer,Commercial,Individual',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive,Suspended'
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Delete customer
     */
    public function destroy(Customer $customer)
    {
        if (!$customer->canBeDeleted()) {
            return back()->with('error', 'Cannot delete customer with outstanding balance or unclosed orders.');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Show pricing management interface
     */
    public function managePricing(Customer $customer)
    {
        $gasTypes = GasType::all();
        $customPrices = $customer->customPrices->keyBy('gas_type_id');
        $tierPrices = CustomerPricingTier::getPricesByType($customer->customer_type);
        
        return view('customers.pricing', compact('customer', 'gasTypes', 'customPrices', 'tierPrices'));
    }

    /**
     * Set custom price for a gas type
     */
    public function setPricing(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'gas_type_id' => 'required|exists:gas_types,id',
            'custom_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        GasTypeCustomerPrice::setPrice(
            $customer->id,
            $validated['gas_type_id'],
            $validated['custom_price'],
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Price updated successfully.');
    }

    /**
     * Remove custom price override
     */
    public function removePricing(Request $request, Customer $customer)
    {
        $request->validate([
            'gas_type_id' => 'required|exists:gas_types,id'
        ]);

        GasTypeCustomerPrice::removePrice($customer->id, $request->gas_type_id);

        return back()->with('success', 'Custom price removed. Category tier pricing now applies.');
    }

    /**
     * Update credit limit
     */
    public function updateCreditLimit(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0'
        ]);

        $customer->update($validated);

        return back()->with('success', 'Credit limit updated successfully.');
    }

    /**
     * Show cylinder tracking and exchange interface
     */
    public function cylinderTracking(Customer $customer)
    {
        $gasTypes = GasType::all();
        $transactions = $customer->cylinderTransactions()
            ->with('gasType')
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);
        $balances = $customer->getAllCylinderBalances();
        
        return view('customers.cylinders', compact('customer', 'gasTypes', 'transactions', 'balances'));
    }

    /**
     * Record cylinder transaction (Issued or Returned)
     */
    public function recordCylinderTransaction(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'gas_type_id' => 'required|exists:gas_types,id',
            'transaction_type' => 'required|in:Issued,Returned',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        // Validate return quantity doesn't exceed balance
        if ($validated['transaction_type'] === 'Returned') {
            $balance = $customer->getCylinderBalance($validated['gas_type_id']);
            if ($validated['quantity'] > $balance) {
                return back()->with('error', "Cannot return {$validated['quantity']} cylinders. Only {$balance} cylinders outstanding.");
            }
        }

        $customer->recordCylinderTransaction(
            $validated['gas_type_id'],
            $validated['transaction_type'],
            $validated['quantity'],
            $validated['reference'] ?? null,
            $validated['notes'] ?? null
        );

        $action = $validated['transaction_type'] === 'Issued' ? 'Issued' : 'Returned';
        return back()->with('success', "{$action} {$validated['quantity']} cylinders successfully.");
    }

    /**
     * Get customer dashboard with comprehensive summary
     */
    public function dashboard(Customer $customer)
    {
        $customer->load('orders', 'customPrices.gasType', 'cylinderTransactions.gasType');
        
        $stats = [
            'total_orders' => $customer->orders()->count(),
            'completed_orders' => $customer->orders()->where('status', 'Completed')->count(),
            'pending_orders' => $customer->orders()->where('status', '!=', 'Completed')->count(),
            'outstanding_balance' => $customer->getOutstandingBalance(),
            'credit_available' => $customer->getCreditAvailable(),
            'is_over_credit' => $customer->isOverCredit(),
            'full_cylinders_net' => $customer->getFullCylindersNet(),
            'custom_prices_count' => $customer->customPrices()->count()
        ];

        $balances = $customer->getAllCylinderBalances();
        $recentTransactions = $customer->getRecentCylinderTransactions(10);
        
        return view('customers.dashboard', compact('customer', 'stats', 'balances', 'recentTransactions'));
    }
}
