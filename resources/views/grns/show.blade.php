@extends('layouts.app')

@section('content')
    <div class="w-full py-4">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex justify-between items-center gap-4 flex-wrap">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Goods Received Note</h1>
                    <p class="text-gray-500">GRN #{{ $grn->grn_number }}</p>
                </div>
                <a href="{{ route('grns.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to GRNs
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- GRN Information Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6">
                        <h5 class="mb-0 text-lg font-semibold">Receipt Information</h5>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">GRN Number</label>
                                <p class="font-semibold text-gray-900">{{ $grn->grn_number }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Supplier</label>
                                <p class="font-semibold text-gray-900">
                                    <a href="{{ route('suppliers.show', $grn->supplier->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $grn->supplier->name }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Purchase Order</label>
                                <p class="font-semibold text-gray-900">
                                    <a href="{{ route('purchase-orders.show', $grn->purchaseOrder->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $grn->purchaseOrder->po_number }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Received Date</label>
                                <p class="font-semibold text-gray-900">{{ $grn->received_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-xs uppercase font-semibold">Approval Status</label>
                                <p>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($grn->approved) bg-green-100 text-green-800
                                        @elseif($grn->status === 'Pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $grn->approved ? 'Approved' : ($grn->status ?? 'Pending') }}
                                    </span>
                                </p>
                            </div>
                            @if($grn->approved_at)
                                <div>
                                    <label class="text-gray-500 text-xs uppercase font-semibold">Approved At</label>
                                    <p class="font-semibold text-gray-900">{{ $grn->approved_at->format('M d, Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                        @if($grn->variance_notes)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <label class="text-gray-500 text-xs uppercase font-semibold">Variance Notes</label>
                                <p class="text-gray-700 mt-1">{{ $grn->variance_notes }}</p>
                            </div>
                        @endif
                        @if($grn->rejection_notes)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <label class="text-gray-500 text-xs uppercase font-semibold">Rejection Notes</label>
                                <p class="text-gray-700 mt-1">{{ $grn->rejection_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="flex flex-col gap-3">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-blue-600 text-sm font-semibold uppercase">Total Items</p>
                    <h3 class="text-3xl font-bold text-blue-900 mt-1">{{ $grn->items->count() }}</h3>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <p class="text-green-600 text-sm font-semibold uppercase">Total Received</p>
                    <h3 class="text-3xl font-bold text-green-900 mt-1">{{ $grn->items->sum('received_qty') }}</h3>
                </div>
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <p class="text-red-600 text-sm font-semibold uppercase">Total Damaged</p>
                    <h3 class="text-3xl font-bold text-red-900 mt-1">{{ $grn->items->sum('damaged_qty') ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-3">
                <h5 class="text-lg font-semibold text-gray-900">Received Items</h5>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Gas Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Ordered Qty</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Received Qty</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Damaged Qty</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rejected Qty</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Variance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grn->items as $item)
                            @php
                                $variance = $item->received_qty - $item->ordered_qty;
                                $varianceColor = $variance > 0 ? 'text-green-600' : ($variance < 0 ? 'text-red-600' : 'text-gray-600');
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->gasType->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->ordered_qty }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $item->received_qty }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->damaged_qty ?? 0 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->rejected_qty ?? 0 }}</td>
                                <td class="px-6 py-4 text-sm font-semibold {{ $varianceColor }}">
                                    @if($variance > 0)
                                        +{{ $variance }}
                                    @elseif($variance < 0)
                                        {{ $variance }}
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-8">
                                    <i class="fas fa-inbox text-2xl opacity-50 mb-2 block"></i>
                                    <p>No items in this GRN</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
