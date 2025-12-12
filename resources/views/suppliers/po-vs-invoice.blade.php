@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('suppliers.dashboard', $supplier->id) }}" class="text-blue-600 hover:underline mb-2 inline-block">← Back to Dashboard</a>
            <h1 class="text-4xl font-bold">{{ $supplier->name }} - PO vs Invoice Analysis</h1>
            <p class="text-gray-600 mt-2">Compare purchase orders with supplier invoices</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total POs</p>
            <h3 class="text-3xl font-bold mt-2">{{ $totalPos }}</h3>
            <p class="text-xs text-green-600 mt-2">{{ $matchedInvoices }} matched invoices</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total PO Amount</p>
            <h3 class="text-3xl font-bold mt-2">Rs. {{ number_format($totalPoAmount, 2) }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total Invoice Amount</p>
            <h3 class="text-3xl font-bold mt-2">Rs. {{ number_format($totalInvoiceAmount, 2) }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Variance</p>
            <h3 class="text-3xl font-bold mt-2" :class="'{{ $totalVariance >= 0 ? 'text-orange-600' : 'text-green-600' }}'">
                Rs. {{ number_format($totalVariance, 2) }}
            </h3>
            <p class="text-xs mt-2" :class="'{{ $totalVariance >= 0 ? 'text-orange-600' : 'text-green-600' }}'">
                {{ $totalVariance >= 0 ? 'Over-invoiced' : 'Under-invoiced' }}
            </p>
        </div>
    </div>

    <!-- Comparison Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-4 font-semibold">PO Number</th>
                    <th class="p-4 font-semibold">PO Date</th>
                    <th class="p-4 font-semibold text-right">PO Amount</th>
                    <th class="p-4 font-semibold">Invoice #</th>
                    <th class="p-4 font-semibold text-right">Invoice Amount</th>
                    <th class="p-4 font-semibold text-right">Variance</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Paid</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($comparisonData as $item)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-semibold text-blue-600">{{ $item['po_number'] }}</td>
                    <td class="p-4">{{ $item['po_date']->format('d M Y') }}</td>
                    <td class="p-4 text-right font-bold">Rs. {{ number_format($item['po_amount'], 2) }}</td>
                    <td class="p-4">
                        @if($item['invoice_number'] != 'N/A')
                            <span class="text-blue-600 font-medium">{{ $item['invoice_number'] }}</span>
                        @else
                            <span class="text-gray-500 text-xs">Not Linked</span>
                        @endif
                    </td>
                    <td class="p-4 text-right font-bold">Rs. {{ number_format($item['invoice_amount'], 2) }}</td>
                    <td class="p-4 text-right">
                        @if($item['variance'] !== null)
                            <span class="px-3 py-1 rounded text-xs font-semibold
                                {{ $item['variance'] > 0 ? 'bg-orange-100 text-orange-700' : ($item['variance'] < 0 ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                Rs. {{ number_format($item['variance'], 2) }}
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            @if($item['matched'])
                                <span class="text-green-600 text-lg">✓</span>
                            @else
                                <span class="text-orange-600 text-lg">!</span>
                            @endif
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $item['po_status'] == 'Completed' ? 'bg-green-100 text-green-700' : ($item['po_status'] == 'Partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $item['po_status'] }}
                            </span>
                        </div>
                    </td>
                    <td class="p-4 text-right">
                        <span class="text-xs font-medium">Rs. {{ number_format($item['total_paid'], 2) }}</span>
                        <div class="text-xs text-gray-500">Remaining: Rs. {{ number_format($item['remaining_balance'], 2) }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-6 text-center text-gray-500">
                        No purchase orders found for this supplier
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Legend -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm font-semibold text-blue-900 mb-3">Legend</p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-green-600 text-lg">✓</span>
                <span>Invoice matched with PO</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-orange-600 text-lg">!</span>
                <span>Invoice variance detected</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="px-2 py-1 rounded text-xs font-semibold bg-orange-100 text-orange-700 whitespace-nowrap">Over-invoiced</span>
                <span class="mt-1">Invoice amount exceeds PO</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700 whitespace-nowrap">Under-invoiced</span>
                <span class="mt-1">Invoice amount below PO</span>
            </div>
        </div>
    </div>

</div>
@endsection
