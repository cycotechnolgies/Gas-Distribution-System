@extends('layouts.app')

@section('content')
<div x-data="paymentForm()" x-init="init()" class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold">Supplier Payments</h1>
            <p class="text-gray-600 mt-2">Manage payments to suppliers</p>
        </div>
        <button @click="openModal=true; resetForm()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Record Payment
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow p-4 flex gap-3">
        <input type="text" placeholder="Search by PO number..." class="flex-1 border border-gray-300 rounded-lg p-2">
        <select class="border border-gray-300 rounded-lg p-2">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Cleared">Cleared</option>
            <option value="Bounced">Bounced</option>
        </select>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-4 font-semibold">Payment Ref</th>
                    <th class="p-4 font-semibold">Supplier</th>
                    <th class="p-4 font-semibold">PO No</th>
                    <th class="p-4 font-semibold">PO Amount</th>
                    <th class="p-4 font-semibold">Paid Amount</th>
                    <th class="p-4 font-semibold">Mode</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-semibold text-blue-600">{{ $payment->payment_ref }}</td>
                    <td class="p-4">
                        <a href="{{ route('supplier-payments.ledger', $payment->supplier->id) }}" class="text-blue-600 hover:underline">
                            {{ $payment->supplier->name }}
                        </a>
                    </td>
                    <td class="p-4 font-medium">{{ $payment->purchaseOrder->po_number }}</td>
                    <td class="p-4">Rs. {{ number_format($payment->po_amount, 2) }}</td>
                    <td class="p-4 font-bold text-green-600">Rs. {{ number_format($payment->payment_amount, 2) }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                            {{ $payment->payment_mode }}
                        </span>
                    </td>
                    <td class="p-4">{{ $payment->payment_date->format('d M Y') }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $payment->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $payment->status == 'Cleared' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $payment->status == 'Bounced' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ $payment->status }}
                        </span>
                    </td>
                    <td class="p-4 flex gap-2">
                        @if($payment->status === 'Pending')
                            <form method="POST" action="{{ route('supplier-payments.status', [$payment->id, 'Cleared']) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs hover:bg-green-200">Clear</button>
                            </form>
                        @endif

                        @if($payment->status !== 'Cleared')
                            <form method="POST" action="{{ route('supplier-payments.destroy', $payment->id) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this payment?')" class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs hover:bg-red-200">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="p-6 text-center text-gray-500">
                        No payments recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $payments->links() }}
    </div>

    <!-- Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-2xl p-8 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6">Record Supplier Payment</h2>

            <form method="POST" action="{{ route('supplier-payments.store') }}">
                @csrf

                <!-- Supplier & PO Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                        <select name="supplier_id" x-model="supplierId" @change="loadPOs()" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Order *</label>
                        <select name="purchase_order_id" x-model="poId" @change="loadPoDetails()" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select PO</option>
                            <template x-for="po in filteredPos" :key="po.id">
                                <option :value="po.id" x-text="`${po.po_number} - Rs. ${po.total_amount}`"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- PO Details Display -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6" x-show="poDetails">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">PO Amount</p>
                            <p class="font-bold text-lg" x-text="`Rs. ${poDetails.po_amount ? poDetails.po_amount.toFixed(2) : '0.00'}`"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Already Paid</p>
                            <p class="font-bold text-lg text-green-600" x-text="`Rs. ${poDetails.paid_amount ? poDetails.paid_amount.toFixed(2) : '0.00'}`"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Remaining</p>
                            <p class="font-bold text-lg text-red-600" x-text="`Rs. ${poDetails.remaining_balance ? poDetails.remaining_balance.toFixed(2) : '0.00'}`"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">PO Date</p>
                            <p class="font-bold" x-text="poDetails.order_date"></p>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Amount *</label>
                        <input type="number" step="0.01" name="payment_amount" x-model.number="paymentAmount" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Mode *</label>
                        <select name="payment_mode" x-model="paymentMode" @change="updatePaymentMode()" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Mode</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                </div>

                <!-- Cheque Details (Conditional) -->
                <div x-show="paymentMode === 'Cheque'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cheque Number</label>
                        <input type="text" name="cheque_number" x-model="chequeNumber" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., 123456">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cheque Date *</label>
                        <input type="date" name="cheque_date" x-model="chequeDate" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Payment Date -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                    <input type="date" name="payment_date" x-model="paymentDate" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" x-model="notes" rows="3" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add any notes about this payment..."></textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button type="button" @click="openModal=false"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        Record Payment
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
function paymentForm() {
    return {
        openModal: false,
        supplierId: '',
        poId: '',
        paymentAmount: 0,
        paymentMode: '',
        chequeNumber: '',
        chequeDate: new Date().toISOString().split('T')[0],
        paymentDate: new Date().toISOString().split('T')[0],
        notes: '',
        poDetails: null,
        filteredPos: [],
        allPos: @json($purchaseOrders),

        resetForm() {
            this.supplierId = '';
            this.poId = '';
            this.paymentAmount = 0;
            this.paymentMode = '';
            this.chequeNumber = '';
            this.chequeDate = new Date().toISOString().split('T')[0];
            this.paymentDate = new Date().toISOString().split('T')[0];
            this.notes = '';
            this.poDetails = null;
            this.filteredPos = [];
        },

        loadPOs() {
            if (!this.supplierId) {
                this.filteredPos = [];
                return;
            }

            this.filteredPos = this.allPos.filter(po => po.supplier_id == this.supplierId);
            this.poId = '';
            this.poDetails = null;
        },

        loadPoDetails() {
            if (!this.poId) {
                this.poDetails = null;
                return;
            }

            fetch(`/supplier-payments/po-details/${this.poId}`)
                .then(res => res.json())
                .then(data => {
                    this.poDetails = data;
                    this.paymentAmount = Math.max(0, data.remaining_balance);
                });
        },

        updatePaymentMode() {
            if (this.paymentMode !== 'Cheque') {
                this.chequeNumber = '';
                this.chequeDate = '';
            }
        },

        init() {
            // Set payment date to today
            this.paymentDate = new Date().toISOString().split('T')[0];
        }
    }
}
</script>
@endsection