@extends('layouts.app')

@section('content')
<div x-data="grnForm()" x-init="init()" class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold">Goods Received Notes (GRN)</h1>
            <p class="text-gray-600 mt-2">Track and manage incoming shipments</p>
        </div>
        <button @click="openModal=true; resetForm()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create GRN
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

    <!-- GRN Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-4 font-semibold">GRN No</th>
                    <th class="p-4 font-semibold">Supplier</th>
                    <th class="p-4 font-semibold">PO Number</th>
                    <th class="p-4 font-semibold">Received Date</th>
                    <th class="p-4 font-semibold">Items</th>
                    <th class="p-4 font-semibold">Quality Issues</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($grns as $grn)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-semibold text-blue-600">{{ $grn->grn_number }}</td>
                    <td class="p-4">{{ $grn->supplier->name }}</td>
                    <td class="p-4 font-medium">{{ $grn->purchaseOrder->po_number }}</td>
                    <td class="p-4">{{ $grn->received_date->format('d M Y') }}</td>
                    <td class="p-4 text-center">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                            {{ $grn->items->count() }} item(s)
                        </span>
                    </td>
                    <td class="p-4">
                        @if($grn->getTotalRejected() > 0 || $grn->getTotalDamaged() > 0)
                            <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-semibold">
                                Rejected: {{ $grn->getTotalRejected() }} | Damaged: {{ $grn->getTotalDamaged() }}
                            </span>
                        @else
                            <span class="text-gray-500 text-xs">None</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            @if($grn->hasShortSupply())
                                <span title="Short supply detected" class="text-yellow-600 text-lg">⚠️</span>
                            @endif
                            @if($grn->hasVariance())
                                <span title="Variance detected" class="text-orange-600 text-lg">📊</span>
                            @endif
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $grn->approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $grn->approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                    </td>
                    <td class="p-4 flex gap-2">
                        @if(!$grn->approved)
                            <form method="POST" action="{{ route('grns.approve', $grn->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs hover:bg-green-200 font-medium">
                                    Approve
                                </button>
                            </form>
                            <form method="POST" action="{{ route('grns.destroy', $grn->id) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this GRN?')" class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs hover:bg-red-200 font-medium">
                                    Delete
                                </button>
                            </form>
                        @else
                            <button class="bg-gray-100 text-gray-700 px-3 py-1 rounded text-xs font-medium cursor-not-allowed" disabled>
                                ✓ Approved
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-6 text-center text-gray-500">
                        No GRNs found. Create one to get started.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $grns->links() }}
    </div>

    <!-- Create GRN Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-4xl p-8 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6">Create Goods Received Note</h2>

            <form method="POST" action="{{ route('grns.store') }}">
                @csrf

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
                                <option :value="po.id" x-text="`${po.po_number} - ${po.items.length} items`"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Received Date *</label>
                        <input type="date" name="received_date" x-model="receivedDate" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- PO Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6" x-show="poDetails">
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">PO Number</p>
                            <p class="font-bold" x-text="poDetails.po_number"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">PO Date</p>
                            <p class="font-bold" x-text="poDetails.po_date"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">PO Status</p>
                            <p class="font-bold" x-text="poDetails.status"></p>
                        </div>
                    </div>
                </div>

                <!-- GRN Items -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Items Received</h3>
                    <div class="space-y-4 border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <template x-for="(item, i) in items" :key="i">
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <!-- Gas Type Name -->
                                <div class="mb-4">
                                    <h4 class="font-bold text-lg" x-text="item.gas_type_name"></h4>
                                    <p class="text-xs text-gray-500" x-text="`Already received: ${item.already_received} | Remaining: ${item.remaining}`"></p>
                                </div>

                                <!-- Quantities Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-4">
                                    <!-- Ordered Qty -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Ordered</label>
                                        <div class="p-2 bg-gray-100 rounded border border-gray-300 text-sm font-bold">
                                            <span x-text="item.ordered_qty"></span>
                                        </div>
                                    </div>

                                    <!-- Received Qty -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Received *</label>
                                        <input type="number" 
                                            :name="`items[${i}][received_qty]`"
                                            x-model.number="item.received_qty"
                                            min="0"
                                            @input="updateReceivedPercentage(i)"
                                            required
                                            class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Damaged Qty -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Damaged</label>
                                        <input type="number" 
                                            :name="`items[${i}][damaged_qty]`"
                                            x-model.number="item.damaged_qty"
                                            min="0"
                                            class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Rejected Qty -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Rejected</label>
                                        <input type="number" 
                                            :name="`items[${i}][rejected_qty]`"
                                            x-model.number="item.rejected_qty"
                                            min="0"
                                            class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Net Received -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Net Received</label>
                                        <div class="p-2 bg-green-100 rounded border border-green-300 text-sm font-bold text-green-700">
                                            <span x-text="(item.received_qty - (item.rejected_qty || 0))"></span>
                                        </div>
                                    </div>

                                    <!-- Variance -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Variance</label>
                                        <div class="p-2 rounded border text-sm font-bold" 
                                            :class="(item.received_qty - item.ordered_qty) < 0 ? 'bg-red-100 text-red-700 border-red-300' : 'bg-orange-100 text-orange-700 border-orange-300'">
                                            <span x-text="item.received_qty - item.ordered_qty"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rejection Notes -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Rejection/Quality Notes</label>
                                    <textarea 
                                        :name="`items[${i}][rejection_notes]`"
                                        x-model="item.rejection_notes"
                                        rows="2"
                                        class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500"
                                        placeholder="Describe any quality issues, damage, or reasons for rejection...">
                                    </textarea>
                                </div>

                                <!-- Alerts -->
                                <div class="mt-3 space-y-1 text-xs">
                                    <template x-if="(item.received_qty - item.ordered_qty) < 0">
                                        <div class="bg-red-50 border border-red-200 text-red-700 p-2 rounded">
                                            ⚠️ Short supply: <span x-text="Math.abs(item.received_qty - item.ordered_qty)"></span> units below ordered
                                        </div>
                                    </template>
                                    <template x-if="(item.received_qty - item.ordered_qty) > 0">
                                        <div class="bg-orange-50 border border-orange-200 text-orange-700 p-2 rounded">
                                            📊 Over delivery: <span x-text="item.received_qty - item.ordered_qty"></span> units above ordered
                                        </div>
                                    </template>
                                    <template x-if="(item.damaged_qty + item.rejected_qty) > 0">
                                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-2 rounded">
                                            ⚠️ Quality issues: <span x-text="item.damaged_qty + item.rejected_qty"></span> units with issues
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="items.length === 0">
                            <div class="text-center py-8 text-gray-500">
                                Select a PO to load items
                            </div>
                        </template>
                    </div>
                </div>

                <!-- General Notes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Variance Notes</label>
                        <textarea name="variance_notes" x-model="varianceNotes" rows="3" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Explain any discrepancies in quantities..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Notes</label>
                        <textarea name="rejection_notes" x-model="rejectionNotes" rows="3" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Explain reasons for rejections..."></textarea>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button type="button" @click="openModal=false"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        Save GRN
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
function grnForm() {
    return {
        openModal: false,
        supplierId: '',
        poId: '',
        receivedDate: new Date().toISOString().split('T')[0],
        varianceNotes: '',
        rejectionNotes: '',
        items: [],
        poDetails: null,
        filteredPos: [],
        allPos: @json($purchaseOrders),

        resetForm() {
            this.supplierId = '';
            this.poId = '';
            this.receivedDate = new Date().toISOString().split('T')[0];
            this.varianceNotes = '';
            this.rejectionNotes = '';
            this.items = [];
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
            this.items = [];
        },

        loadPoDetails() {
            if (!this.poId) {
                this.poDetails = null;
                this.items = [];
                return;
            }

            fetch(`/grns/po-details/${this.poId}`)
                .then(res => res.json())
                .then(data => {
                    this.poDetails = data;
                    this.items = data.items.map(item => ({
                        ...item,
                        received_qty: item.remaining,
                        damaged_qty: 0,
                        rejected_qty: 0,
                        rejection_notes: ''
                    }));
                });
        },

        updateReceivedPercentage(index) {
            // Auto-calculate on input change
        },

        init() {
            this.receivedDate = new Date().toISOString().split('T')[0];
        }
    }
}
</script>
@endsection
