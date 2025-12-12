@extends('layouts.app')

@section('content')
    <div class="w-full py-4">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex justify-between items-center gap-4 flex-wrap">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Purchase Order Details</h1>
                    <p class="text-gray-500">PO #{{ $purchaseOrder->po_number }}</p>
                </div>
                <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Purchase Orders
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- PO Information Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6">
                        <h5 class="mb-0 text-lg font-semibold">Order Information</h5>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">PO Number</label>
                                <p class="font-semibold text-gray-900">{{ $purchaseOrder->po_number }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Supplier</label>
                                <p class="font-semibold text-gray-900">
                                    <a href="{{ route('suppliers.show', $purchaseOrder->supplier->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $purchaseOrder->supplier->name }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Order Date</label>
                                <p class="font-semibold text-gray-900">{{ $purchaseOrder->order_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Delivery Date</label>
                                <p class="font-semibold text-gray-900">{{ $purchaseOrder->delivery_date?->format('M d, Y') ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Total Amount</label>
                                <p class="font-semibold text-lg text-gray-900">LKR {{ number_format($purchaseOrder->total_amount, 2) }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Status</label>
                                <p>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($purchaseOrder->status === 'Completed') bg-green-100 text-green-800
                                        @elseif($purchaseOrder->status === 'Pending') bg-yellow-100 text-yellow-800
                                        @elseif($purchaseOrder->status === 'Approved') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $purchaseOrder->status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        @if($purchaseOrder->notes)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <label class="text-gray-500 text-xs uppercase font-semibold">Notes</label>
                                <p class="text-gray-700 mt-1">{{ $purchaseOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="flex flex-col gap-3">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-blue-600 text-sm font-semibold uppercase">Total Items</p>
                    <h3 class="text-3xl font-bold text-blue-900 mt-1">{{ $purchaseOrder->items->count() }}</h3>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <p class="text-purple-600 text-sm font-semibold uppercase">Total GRNs</p>
                    <h3 class="text-3xl font-bold text-purple-900 mt-1">{{ $purchaseOrder->grns->count() }}</h3>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-600 text-sm font-semibold uppercase">Items Received</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $purchaseOrder->grns->sum(function($g) { return $g->items->count(); }) }}</h3>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-3">
                <h5 class="text-lg font-semibold text-gray-900">Order Items</h5>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Gas Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Unit Price</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrder->items as $item)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->gasType->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">LKR {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">LKR {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 py-8">
                                    <i class="fas fa-inbox text-2xl opacity-50 mb-2 block"></i>
                                    <p>No items in this purchase order</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GRNs Section -->
        @if($purchaseOrder->grns->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-3">
                    <h5 class="text-lg font-semibold text-gray-900">Goods Received Notes</h5>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">GRN Number</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Received Date</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Items</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrder->grns as $grn)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $grn->grn_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $grn->received_date?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $grn->items->count() }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($grn->approved) bg-green-100 text-green-800
                                            @elseif($grn->status === 'Pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $grn->approved ? 'Approved' : ($grn->status ?? 'Pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('grns.show', $grn->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-4">No GRNs created yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
