@extends('layouts.app')

@section('content')
<div x-data="invoiceForm()" class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold">Supplier Invoices</h1>
            <p class="text-gray-600 mt-2">Manage supplier billing and reconciliation</p>
        </div>
        <button @click="openModal=true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Record Invoice
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-4 font-semibold">Invoice #</th>
                    <th class="p-4 font-semibold">Supplier</th>
                    <th class="p-4 font-semibold">PO Number</th>
                    <th class="p-4 font-semibold">Invoice Date</th>
                    <th class="p-4 font-semibold">Amount</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-semibold text-blue-600">{{ $invoice->invoice_number }}</td>
                    <td class="p-4">
                        <a href="{{ route('suppliers.dashboard', $invoice->supplier->id) }}" class="text-blue-600 hover:underline">
                            {{ $invoice->supplier->name }}
                        </a>
                    </td>
                    <td class="p-4 font-medium">
                        @if($invoice->purchaseOrder)
                            {{ $invoice->purchaseOrder->po_number }}
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="p-4">{{ $invoice->invoice_date->format('d M Y') }}</td>
                    <td class="p-4 font-bold">Rs. {{ number_format($invoice->invoice_amount, 2) }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $invoice->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $invoice->status == 'Reconciled' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $invoice->status == 'Disputed' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td class="p-4 flex gap-2">
                        @if($invoice->status !== 'Reconciled')
                            <form method="POST" action="{{ route('supplier-invoices.status', [$invoice->id, 'Reconciled']) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs hover:bg-green-200">Reconcile</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('supplier-invoices.destroy', $invoice->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this invoice?')" class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs hover:bg-red-200">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        No invoices recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $invoices->links() }}
    </div>

    <!-- Create Invoice Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-2xl p-8 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6">Record Supplier Invoice</h2>

            <form method="POST" action="{{ route('supplier-invoices.store') }}">
                @csrf

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                        <select name="supplier_id" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Order (Optional)</label>
                        <select name="purchase_order_id" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Link to PO</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date *</label>
                        <input type="date" name="invoice_date" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Amount *</label>
                        <input type="number" step="0.01" name="invoice_amount" min="0.01" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Reconciled">Reconciled</option>
                        <option value="Disputed">Disputed</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="2" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Invoice description..."></textarea>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Additional notes..."></textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button type="button" @click="openModal=false"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        Record Invoice
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
function invoiceForm() {
    return {
        openModal: false
    }
}
</script>
@endsection
