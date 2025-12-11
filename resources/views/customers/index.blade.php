@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-4xl font-bold text-gray-900">Customer Management</h1>
            <a href="{{ route('customers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                + Add New Customer
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Customers Table -->
        <div class="bg-white overflow-hidden shadow-md rounded-lg">
            @if($customers->count() > 0)
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">City</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Credit Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Outstanding</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                        {{ $customer->customer_type === 'Dealer' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $customer->customer_type === 'Commercial' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $customer->customer_type === 'Individual' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ $customer->customer_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->phone ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->city ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    LKR {{ number_format($customer->credit_limit ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <span class="px-3 py-1 rounded-full 
                                        {{ $customer->getOutstandingBalance() > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        LKR {{ number_format($customer->getOutstandingBalance(), 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $customer->status === 'Active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $customer->status === 'Inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $customer->status === 'Suspended' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $customer->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="text-orange-600 hover:text-orange-800">Edit</a>
                                    <a href="{{ route('customers.pricing', $customer) }}" class="text-purple-600 hover:text-purple-800">Pricing</a>
                                    <a href="{{ route('customers.cylinders', $customer) }}" class="text-teal-600 hover:text-teal-800">Cylinders</a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $customers->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">No customers found. Start by adding a new customer.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
