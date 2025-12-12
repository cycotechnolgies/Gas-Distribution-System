@extends('layouts.app')

@section('title', 'Cylinder Tracking - ' . $customer->name)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('customers.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Customers</a>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Cylinder Tracking</h1>
            <p class="text-gray-600 mt-2">Customer: <span class="font-semibold">{{ $customer->name }}</span></p>
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

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <!-- Summary Cards -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Total Cylinders Issued</p>
                <p class="text-3xl font-bold text-blue-600">{{ $customer->full_cylinders_issued ?? 0 }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Total Cylinders Returned</p>
                <p class="text-3xl font-bold text-green-600">{{ $customer->empty_cylinders_returned ?? 0 }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Net Cylinders Outstanding</p>
                <p class="text-3xl font-bold text-red-600">{{ $customer->getFullCylindersNet() }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <p class="text-xs text-gray-600 uppercase font-semibold">Total Transactions</p>
                <p class="text-3xl font-bold text-purple-600">{{ $transactions->total() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Record Transaction -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Record Transaction</h2>

                <form action="{{ route('customers.recordCylinderTransaction', $customer) }}" method="POST">
                    @csrf

                    <!-- Gas Type -->
                    <div class="mb-4">
                        <label for="gas_type_id" class="block text-sm font-medium text-gray-700 mb-2">Gas Type <span class="text-red-600">*</span></label>
                        <select name="gas_type_id" id="gas_type_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Type --</option>
                            @foreach($gasTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('gas_type_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Type -->
                    <div class="mb-4">
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-600">*</span></label>
                        <select name="transaction_type" id="transaction_type" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Type --</option>
                            <option value="Issued">Issued (Full Cylinders)</option>
                            <option value="Returned">Returned (Empty Cylinders)</option>
                        </select>
                        @error('transaction_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-600">*</span></label>
                        <input type="number" name="quantity" id="quantity" min="1" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Number of cylinders">
                        @error('quantity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference -->
                    <div class="mb-4">
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                        <input type="text" name="reference" id="reference"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Order #, Delivery ID, etc.">
                        @error('reference')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Any additional notes..."></textarea>
                        @error('notes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Record Transaction
                    </button>
                </form>
            </div>

            <!-- Right: Cylinder Balances by Type -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Cylinder Balance by Type</h2>

                    @if(array_filter($balances, fn($b) => $b['balance'] != 0))
                        <div class="space-y-3">
                            @foreach($balances as $gasTypeId => $balance)
                                @if($balance['balance'] != 0)
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $balance['name'] }}</p>
                                        </div>
                                        <span class="px-4 py-2 rounded-full font-bold text-lg {{ $balance['balance'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $balance['balance'] }} {{ $balance['balance'] == 1 ? 'cylinder' : 'cylinders' }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No outstanding cylinders</p>
                    @endif
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Transactions</h2>

                    @if($transactions->count() > 0)
                        <div class="space-y-2">
                            @foreach($transactions as $transaction)
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border-l-4 {{ $transaction->transaction_type === 'Issued' ? 'border-red-500' : 'border-green-500' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900">{{ $transaction->gasType->name }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full {{ $transaction->transaction_type === 'Issued' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $transaction->transaction_type }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">{{ $transaction->transaction_date->format('d M Y') }}</p>
                                        @if($transaction->reference)
                                            <p class="text-xs text-gray-500">Ref: {{ $transaction->reference }}</p>
                                        @endif
                                    </div>
                                    <span class="font-bold text-lg text-gray-900">{{ $transaction->quantity }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 border-t pt-6">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 italic">No transactions recorded yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
