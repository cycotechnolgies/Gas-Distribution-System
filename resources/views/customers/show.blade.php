@extends('layouts.app')

@section('title', $customer->name)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('customers.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Customers</a>
            </div>
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">{{ $customer->name }}</h1>
                    <p class="text-gray-600 mt-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            {{ $customer->customer_type === 'Dealer' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $customer->customer_type === 'Commercial' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $customer->customer_type === 'Individual' ? 'bg-green-100 text-green-800' : '' }}">
                            {{ $customer->customer_type }}
                        </span>
                        <span class="ml-2 px-3 py-1 rounded-full text-sm font-medium
                            {{ $customer->status === 'Active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $customer->status === 'Inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $customer->status === 'Suspended' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $customer->status }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('customers.edit', $customer) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit
                </a>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Outstanding Balance</p>
                <p class="text-3xl font-bold {{ $stats['outstanding_balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                    LKR {{ number_format($stats['outstanding_balance'], 2) }}
                </p>
                @if($stats['is_over_credit'])
                    <p class="text-xs text-red-600 mt-2">⚠️ Over credit limit</p>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Credit Available</p>
                <p class="text-3xl font-bold {{ $stats['credit_available'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    LKR {{ number_format($stats['credit_available'], 2) }}
                </p>
                <p class="text-xs text-gray-600 mt-2">Limit: LKR {{ number_format($customer->credit_limit, 2) }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Net Cylinders</p>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['full_cylinders_net'] }}</p>
                <p class="text-xs text-gray-600 mt-2">Issued: {{ $customer->full_cylinders_issued ?? 0 }} | Returned: {{ $customer->empty_cylinders_returned ?? 0 }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Custom Prices</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['custom_prices_count'] }}</p>
                <a href="{{ route('customers.pricing', $customer) }}" class="text-xs text-purple-600 hover:text-purple-800 mt-2">Manage Pricing →</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Customer Info -->
            <div class="lg:col-span-2">
                <!-- Contact Information -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Phone</p>
                            <p class="text-lg text-gray-900">{{ $customer->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Email</p>
                            <p class="text-lg text-gray-900">{{ $customer->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">City</p>
                            <p class="text-lg text-gray-900">{{ $customer->city ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">NIC / ID</p>
                            <p class="text-lg text-gray-900">{{ $customer->nic ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-600 uppercase font-semibold">Address</p>
                            <p class="text-lg text-gray-900">{{ $customer->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cylinder Balances -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Cylinder Balance by Type</h2>
                        <a href="{{ route('customers.cylinders', $customer) }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
                    </div>

                    @if(array_filter($balances, fn($b) => $b['balance'] != 0))
                        <div class="space-y-3">
                            @foreach($balances as $gasTypeId => $balance)
                                @if($balance['balance'] != 0)
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <p class="font-semibold text-gray-900">{{ $balance['name'] }}</p>
                                        <span class="px-4 py-2 rounded-full font-bold text-lg {{ $balance['balance'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $balance['balance'] }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No outstanding cylinders</p>
                    @endif
                </div>
            </div>

            <!-- Right: Summary & Actions -->
            <div>
                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="{{ route('customers.cylinders', $customer) }}" class="block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center font-medium">
                            Manage Cylinders
                        </a>
                        <a href="{{ route('customers.pricing', $customer) }}" class="block px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-center font-medium">
                            Pricing Management
                        </a>
                        <form action="{{ route('customers.updateCreditLimit', $customer) }}" method="POST" class="space-y-2">
                            @csrf
                            <div class="flex gap-2">
                                <input type="number" step="0.01" name="credit_limit" value="{{ $customer->credit_limit }}" required
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                    placeholder="New limit">
                                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-medium">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Statistics -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Order Statistics</h2>
                    <div class="space-y-3">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-600 uppercase font-semibold">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-600 uppercase font-semibold">Completed</p>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['completed_orders'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-600 uppercase font-semibold">Pending</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_orders'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cylinder Transactions -->
        @if($recentTransactions->count() > 0)
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Cylinder Transactions</h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Date</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Gas Type</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Type</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Quantity</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-900">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $transaction->transaction_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $transaction->gasType->name }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $transaction->transaction_type === 'Issued' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $transaction->transaction_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $transaction->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $transaction->reference ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
