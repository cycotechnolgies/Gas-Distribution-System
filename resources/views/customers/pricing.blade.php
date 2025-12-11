@extends('layouts.app')

@section('title', 'Customer Pricing - ' . $customer->name)

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('customers.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Customers</a>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Pricing Management</h1>
            <p class="text-gray-600 mt-2">Customer: <span class="font-semibold">{{ $customer->name }}</span> ({{ $customer->customer_type }})</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel: Custom Prices -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Custom Price Overrides</h2>
                    <p class="text-gray-600 mb-6">Set custom prices for specific gas types. Leave empty to use category tier pricing.</p>

                    @if($customPrices->count() > 0)
                        <div class="space-y-4 mb-6">
                            @foreach($customPrices as $gasTypeId => $price)
                                <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $price->gasType->name }}</p>
                                        <p class="text-sm text-gray-600">Custom Price: <span class="font-bold text-blue-600">LKR {{ number_format($price->custom_price, 2) }}</span></p>
                                        @if($price->notes)
                                            <p class="text-xs text-gray-500 mt-1">{{ $price->notes }}</p>
                                        @endif
                                    </div>
                                    <form action="{{ route('customers.removePricing', $customer) }}" method="POST" onsubmit="return confirm('Remove custom price?');">
                                        @csrf
                                        <input type="hidden" name="gas_type_id" value="{{ $gasTypeId }}">
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Remove</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic mb-6">No custom prices set. All prices will use category tier pricing.</p>
                    @endif

                    <!-- Add/Update Custom Price Form -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $customPrices->count() > 0 ? 'Update' : 'Set' }} Custom Price</h3>
                        <form action="{{ route('customers.setPricing', $customer) }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Gas Type Selection -->
                                <div>
                                    <label for="gas_type_id" class="block text-sm font-medium text-gray-700 mb-2">Gas Type <span class="text-red-600">*</span></label>
                                    <select name="gas_type_id" id="gas_type_id" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Gas Type --</option>
                                        @foreach($gasTypes as $gasType)
                                            <option value="{{ $gasType->id }}">{{ $gasType->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('gas_type_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Custom Price -->
                                <div>
                                    <label for="custom_price" class="block text-sm font-medium text-gray-700 mb-2">Price (LKR) <span class="text-red-600">*</span></label>
                                    <input type="number" step="0.01" name="custom_price" id="custom_price" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="0.00">
                                    @error('custom_price')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="flex items-end">
                                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                        Set Price
                                    </button>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mt-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <input type="text" name="notes" id="notes" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="e.g., Special agreement, discount reason, etc.">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Category Tier Pricing -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Category Tier Pricing</h2>
                    <p class="text-sm text-gray-600 mb-4">Fallback prices for {{ $customer->customer_type }} category</p>

                    @if($tierPrices->count() > 0)
                        <div class="space-y-3">
                            @foreach($tierPrices as $tier)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $tier->gasType->name }}</p>
                                    <p class="text-lg font-bold text-green-600">LKR {{ number_format($tier->price, 2) }}</p>
                                    @if($tier->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $tier->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">
                                No tier pricing configured for {{ $customer->customer_type }} category yet. 
                                Custom prices will be required.
                            </p>
                        </div>
                    @endif

                    <!-- Info -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-xs text-blue-800">
                            <strong>How it works:</strong> Custom price overrides will be used for this customer. 
                            If no custom price is set, the category tier price is applied.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
