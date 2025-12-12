@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="py-6 sm:py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Customer Management</h1>
            <a href="{{ route('customers.create') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 rounded-lg font-medium text-center text-sm sm:text-base">
                + Add New Customer
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 sm:py-4 rounded-lg text-sm sm:text-base">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 sm:py-4 rounded-lg text-sm sm:text-base">
                {{ session('error') }}
            </div>
        @endif

        @if($customers->count() > 0)
            <!-- Desktop Table View (md and above) -->
            <div class="hidden md:block bg-white overflow-hidden shadow-md rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Type</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Phone</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">City</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Credit Limit</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Outstanding</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 lg:px-6 py-4">
                                        <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm lg:text-base">
                                            {{ $customer->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <span class="px-2 lg:px-3 py-1 rounded-full text-xs lg:text-sm font-medium 
                                            {{ $customer->customer_type === 'Dealer' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $customer->customer_type === 'Commercial' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $customer->customer_type === 'Individual' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ $customer->customer_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-xs lg:text-sm text-gray-600">{{ $customer->phone ?? '-' }}</td>
                                    <td class="px-4 lg:px-6 py-4 text-xs lg:text-sm text-gray-600">{{ $customer->city ?? '-' }}</td>
                                    <td class="px-4 lg:px-6 py-4 text-xs lg:text-sm font-medium">
                                        LKR {{ number_format($customer->credit_limit ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-xs lg:text-sm font-medium">
                                        <span class="px-2 lg:px-3 py-1 rounded-full 
                                            {{ $customer->getOutstandingBalance() > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            LKR {{ number_format($customer->getOutstandingBalance(), 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-medium
                                            {{ $customer->status === 'Active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $customer->status === 'Inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $customer->status === 'Suspended' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $customer->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-xs lg:text-sm space-x-1 flex gap-1">
                                        <a href="{{ route('customers.show', $customer) }}" class="bg-blue-200 text-blue-600 hover:bg-blue-300 px-2 py-1 rounded-md">View</a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="bg-orange-200 text-orange-600 hover:bg-orange-300 px-2 py-1 rounded-md">Edit</a>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-200 text-red-600 hover:bg-red-300 px-2 py-1 rounded-md">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 lg:px-6 py-4 border-t border-gray-200">
                    {{ $customers->links() }}
                </div>
            </div>

            <!-- Mobile Card View (below md) -->
            <div class="md:hidden space-y-4">
                @foreach($customers as $customer)
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-4 py-3 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">
                                {{ $customer->name }}
                            </h3>
                            <p class="text-xs text-gray-600 mt-1">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $customer->customer_type === 'Dealer' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $customer->customer_type === 'Commercial' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $customer->customer_type === 'Individual' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ $customer->customer_type }}
                                </span>
                            </p>
                        </div>

                        <!-- Card Content -->
                        <div class="px-4 py-3 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-600 font-medium">Phone</p>
                                    <p class="text-sm text-gray-900">{{ $customer->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-medium">City</p>
                                    <p class="text-sm text-gray-900">{{ $customer->city ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-medium">Credit Limit</p>
                                    <p class="text-sm font-semibold text-gray-900">LKR {{ number_format($customer->credit_limit ?? 0, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-medium">Outstanding</p>
                                    <p class="text-sm font-semibold {{ $customer->getOutstandingBalance() > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        LKR {{ number_format($customer->getOutstandingBalance(), 2) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="pt-2 border-t border-gray-200">
                                <p class="text-xs text-gray-600 font-medium mb-1">Status</p>
                                <span class="px-3 py-1 rounded-full text-xs font-medium inline-block
                                    {{ $customer->status === 'Active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $customer->status === 'Inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $customer->status === 'Suspended' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $customer->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="bg-gray-50 px-4 py-3 flex gap-2 border-t border-gray-200">
                            <a href="{{ route('customers.show', $customer) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md text-center text-sm font-medium">
                                View
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-md text-center text-sm font-medium">
                                Edit
                            </a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md text-sm font-medium">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- Mobile Pagination -->
                <div class="mt-6">
                    {{ $customers->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md py-12">
                <p class="text-center text-gray-500 text-base sm:text-lg">No customers found. Start by adding a new customer.</p>
            </div>
        @endif
    </div>
</div>
@endsection
