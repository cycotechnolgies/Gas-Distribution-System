<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(12);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'nic' => 'nullable',
            'city' => 'nullable',
            'customer_type' => 'required',
            'credit_limit' => 'nullable|numeric'
        ]);

        Customer::create($request->all());

        return back()->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email'
        ]);

        $customer->update($request->all());

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer deleted successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load('orders'); // future use
        return view('customers.show', compact('customer'));
    }
}
