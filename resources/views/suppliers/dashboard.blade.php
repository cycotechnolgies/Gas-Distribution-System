@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('suppliers.index') }}" class="text-blue-600 hover:underline">← Suppliers</a>
                <h1 class="text-4xl font-bold">{{ $supplier->name }}</h1>
            </div>
            <p class="text-gray-600">Comprehensive supplier performance dashboard</p>
        </div>
        <div class="space-y-2">
            <a href="{{ route('suppliers.po-vs-invoice', $supplier->id) }}" class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                PO vs Invoice Report
            </a>
            <a href="{{ route('suppliers.refill-analysis', $supplier->id) }}" class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                Refill Analysis
            </a>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total PO Value -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total PO Value</p>
                    <h3 class="text-3xl font-bold mt-2">Rs. {{ number_format($totalPoValue, 2) }}</h3>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
            </div>
        </div>

        <!-- Total Paid -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Amount Paid</p>
                    <h3 class="text-3xl font-bold mt-2 text-green-600">Rs. {{ number_format($totalPaid, 2) }}</h3>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Outstanding Balance</p>
                    <h3 class="text-3xl font-bold mt-2 text-red-600">Rs. {{ number_format($outstanding, 2) }}</h3>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                </div>
            </div>
        </div>

        <!-- Refill Cost -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Refill Cost</p>
                    <h3 class="text-3xl font-bold mt-2 text-purple-600">Rs. {{ number_format($totalRefillCost, 2) }}</h3>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- PO & Refill Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- PO Summary -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Purchase Orders</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total POs</span>
                    <span class="font-bold">{{ $totalPos }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Completed</span>
                    <span class="font-bold text-green-600">{{ $completedPos }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Partial</span>
                    <span class="font-bold text-yellow-600">{{ $partialPos }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pending</span>
                    <span class="font-bold text-gray-600">{{ $pendingPos }}</span>
                </div>
                <div class="border-t pt-2 flex justify-between font-bold">
                    <span>Completion Rate</span>
                    <span class="text-blue-600">{{ $completionRate }}%</span>
                </div>
            </div>
        </div>

        <!-- Refill Summary -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Refill Summary</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Refills</span>
                    <span class="font-bold">{{ $refillsByType->sum(fn($r) => $r['refill_count']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cylinders Refilled</span>
                    <span class="font-bold">{{ $totalCylindersRefilled }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Cost</span>
                    <span class="font-bold">Rs. {{ number_format($totalRefillCost, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Performance -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Payment Performance</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Avg Payment Days</span>
                    <span class="font-bold">{{ $averagePaymentDays }} days</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cylinders Received</span>
                    <span class="font-bold">{{ $totalCylindersReceived }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Outstanding</span>
                    <span class="font-bold text-red-600">Rs. {{ number_format($outstanding, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Refills by Type -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Refills by Gas Type</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-3 font-semibold">Gas Type</th>
                        <th class="p-3 font-semibold text-right">Cylinders Refilled</th>
                        <th class="p-3 font-semibold text-right">Avg Cost/Unit</th>
                        <th class="p-3 font-semibold text-right">Total Cost</th>
                        <th class="p-3 font-semibold text-center">Count</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($refillsByType as $refill)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $refill['gas_type'] }}</td>
                        <td class="p-3 text-right font-bold">{{ $refill['total_cylinders'] }}</td>
                        <td class="p-3 text-right">Rs. {{ number_format($refill['average_cost'], 2) }}</td>
                        <td class="p-3 text-right font-bold">Rs. {{ number_format($refill['total_cost'], 2) }}</td>
                        <td class="p-3 text-center">{{ $refill['refill_count'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No refills recorded for this supplier
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent POs -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Purchase Orders</h3>
            <div class="space-y-3">
                @forelse($recentPos as $po)
                <div class="border-b pb-3 last:border-b-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-blue-600">{{ $po->po_number }}</p>
                            <p class="text-xs text-gray-500">{{ $po->order_date->format('d M Y') }}</p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            {{ $po->status == 'Completed' ? 'bg-green-100 text-green-700' : ($po->status == 'Partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ $po->status }}
                        </span>
                    </div>
                    <p class="text-sm font-bold mt-1">Rs. {{ number_format($po->total_amount, 2) }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No recent POs</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Payments</h3>
            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                <div class="border-b pb-3 last:border-b-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-blue-600">{{ $payment->payment_ref }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->payment_date->format('d M Y') }}</p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            {{ $payment->status == 'Cleared' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $payment->status }}
                        </span>
                    </div>
                    <p class="text-sm font-bold mt-1">Rs. {{ number_format($payment->payment_amount, 2) }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No recent payments</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
