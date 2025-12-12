@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('supplier-payments.index') }}" class="text-blue-600 hover:underline">← Back</a>
                <h1 class="text-4xl font-bold">{{ $supplier->name }}</h1>
            </div>
            <p class="text-gray-600">Supplier Ledger & Payment History</p>
        </div>
    </div>

    <!-- Summary Cards -->
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

        <!-- Overpayment -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Overpayment Credit</p>
                    <h3 class="text-3xl font-bold mt-2 text-orange-600">Rs. {{ number_format($overpayment, 2) }}</h3>
                </div>
                <div class="bg-orange-100 p-4 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-600"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-4 font-semibold">PO Number</th>
                    <th class="p-4 font-semibold">PO Date</th>
                    <th class="p-4 font-semibold text-right">PO Amount</th>
                    <th class="p-4 font-semibold text-right">Amount Paid</th>
                    <th class="p-4 font-semibold text-right">Balance</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Payment Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($ledgerData as $item)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-semibold text-blue-600">{{ $item['po_number'] }}</td>
                    <td class="p-4">{{ $item['po_date']->format('d M Y') }}</td>
                    <td class="p-4 text-right">Rs. {{ number_format($item['po_amount'], 2) }}</td>
                    <td class="p-4 text-right font-bold text-green-600">Rs. {{ number_format($item['paid_amount'], 2) }}</td>
                    <td class="p-4 text-right font-bold {{ $item['remaining_balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rs. {{ number_format($item['remaining_balance'], 2) }}
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $item['status'] == 'Paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $item['status'] }}
                        </span>
                    </td>
                    <td class="p-4">
                        @if($item['po']->payments->count() > 0)
                            <details class="cursor-pointer">
                                <summary class="text-blue-600 hover:underline">{{ $item['po']->payments->count() }} Payment(s)</summary>
                                <div class="mt-2 bg-gray-50 p-3 rounded text-xs space-y-1">
                                    @foreach($item['po']->payments as $payment)
                                        <div class="flex justify-between">
                                            <span>{{ $payment->payment_ref }} ({{ $payment->payment_mode }})</span>
                                            <span class="font-bold">Rs. {{ number_format($payment->payment_amount, 2) }}</span>
                                            <span class="px-2 py-0.5 rounded text-white text-xs
                                                {{ $payment->status == 'Cleared' ? 'bg-green-500' : ($payment->status == 'Pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                                {{ $payment->status }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <span class="text-gray-500 text-xs">No payments</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        No purchase orders found for this supplier.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection