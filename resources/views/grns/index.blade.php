@extends('layouts.app')

@section('content')
<div x-data="grnForm()" x-init="init()" class="space-y-4 sm:space-y-6 lg:space-y-8 px-2 sm:px-4">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-4xl font-bold">Goods Received Notes</h1>
        </div>
        <button @click="openModal=true; resetForm()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 lg:px-6 py-2 sm:py-2 lg:py-3 rounded-lg sm:rounded-xl shadow-lg font-semibold flex items-center gap-2 text-xs sm:text-sm lg:text-base whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span class="hidden sm:inline">Create GRN</span><span class="sm:hidden">Add</span>
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 rounded text-sm sm:text-base">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 rounded text-sm sm:text-base">
            {{ session('error') }}
        </div>
    @endif

    <!-- GRN Table - Desktop View -->
    <div class="hidden md:block bg-white rounded-lg sm:rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm lg:text-base">
            <thead class="border-b">
                <tr>
                    <th class="p-3 lg:p-4 font-semibold text-left">GRN No</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Supplier</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">PO Number</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Received Date</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Items</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Quality Issues</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Status</th>
                    <th class="p-3 lg:p-4 font-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($grns as $grn)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 lg:p-4 font-semibold text-blue-600">{{ $grn->grn_number }}</td>
                    <td class="p-3 lg:p-4">{{ $grn->supplier->name }}</td>
                    <td class="p-3 lg:p-4 font-medium">{{ $grn->purchaseOrder->po_number }}</td>
                    <td class="p-3 lg:p-4">{{ $grn->received_date->format('d M Y') }}</td>
                    <td class="p-3 lg:p-4 text-center">
                        <span class="text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                            {{ $grn->items->count() }}
                        </span>
                    </td>
                    <td class="p-3 lg:p-4">
                        @if($grn->getTotalRejected() > 0 || $grn->getTotalDamaged() > 0)
                            <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-semibold">
                                R: {{ $grn->getTotalRejected() }} | D: {{ $grn->getTotalDamaged() }}
                            </span>
                        @else
                            <span class="text-gray-500 text-xs">None</span>
                        @endif
                    </td>
                    <td class="p-3 lg:p-4">
                        <div class="flex items-center gap-2">
                            @if($grn->hasShortSupply())
                                <span title="Short supply detected" class="text-yellow-600 text-lg">⚠️</span>
                            @endif
                            @if($grn->hasVariance())
                                <span title="Variance detected" class="text-orange-600 text-lg">📊</span>
                            @endif
                            <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-semibold
                                {{ $grn->approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $grn->approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                    </td>
                    <td class="p-3 lg:p-4 flex gap-1 lg:gap-2 flex-wrap justify-center">
                        @if(!$grn->approved)
                            <form method="POST" action="{{ route('grns.approve', $grn->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-100 text-green-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm hover:bg-green-200 font-medium">
                                    Approve
                                </button>
                            </form>
                            <a href="{{ route('grns.show', $grn->id) }}" class="bg-indigo-100 text-indigo-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm hover:bg-indigo-200 font-medium">
                                View
                            </a>
                            <form method="POST" action="{{ route('grns.destroy', $grn->id) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this GRN?')" class="bg-red-100 text-red-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm hover:bg-red-200 font-medium">
                                    Delete
                                </button>
                            </form>
                        @else
                            <a href="{{ route('grns.show', $grn->id) }}" class="bg-indigo-100 text-indigo-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm hover:bg-indigo-200 font-medium">
                                View
                            </a>
                            <button class="bg-gray-100 text-gray-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm font-medium cursor-not-allowed" disabled>
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

    <!-- GRN Cards - Mobile View -->
    <div class="md:hidden space-y-3 sm:space-y-4">
        @forelse($grns as $grn)
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
                <div class="flex justify-between items-start gap-2 mb-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">GRN Number</p>
                        <p class="text-base sm:text-lg font-bold text-blue-600">{{ $grn->grn_number }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                        {{ $grn->approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $grn->approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>

                <div class="space-y-2 mb-3 pb-3 border-b border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">Supplier:</span>
                        <span class="font-medium truncate">{{ $grn->supplier->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">PO:</span>
                        <span class="font-medium">{{ $grn->purchaseOrder->po_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">Received:</span>
                        <span class="font-medium">{{ $grn->received_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">Items:</span>
                        <span class="font-medium">{{ $grn->items->count() }}</span>
                    </div>
                    @if($grn->getTotalRejected() > 0 || $grn->getTotalDamaged() > 0)
                        <div class="flex justify-between text-sm bg-orange-50 p-2 rounded">
                            <span class="text-gray-500 font-semibold">Quality Issues:</span>
                            <span class="font-medium text-orange-700">R: {{ $grn->getTotalRejected() }} | D: {{ $grn->getTotalDamaged() }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('grns.show', $grn->id) }}" class="flex-1 min-w-[70px] bg-indigo-100 text-indigo-700 px-2 py-1.5 rounded text-xs font-semibold hover:bg-indigo-200 text-center">
                        View
                    </a>
                    @if(!$grn->approved)
                        <form method="POST" action="{{ route('grns.approve', $grn->id) }}" class="flex-1 min-w-[70px]">
                            @csrf
                            <button type="submit" class="w-full bg-green-100 text-green-700 px-2 py-1.5 rounded text-xs font-semibold hover:bg-green-200">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('grns.destroy', $grn->id) }}" class="flex-1 min-w-[70px]">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this GRN?')" class="w-full bg-red-100 text-red-700 px-2 py-1.5 rounded text-xs font-semibold hover:bg-red-200">
                                Delete
                            </button>
                        </form>
                    @else
                        <button class="flex-1 min-w-[70px] bg-gray-100 text-gray-700 px-2 py-1.5 rounded text-xs font-semibold" disabled>
                            ✓ Approved
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg p-8 text-center">
                <p class="text-gray-500">No GRNs found. Create one to get started.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        {{ $grns->links() }}
    </div>

    <!-- Create GRN Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-2 sm:p-4 z-50 overflow-y-auto">
        <div class="bg-white w-full max-w-2xl lg:max-w-4xl p-4 sm:p-6 lg:p-8 rounded-lg sm:rounded-xl shadow-2xl my-auto">

            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold">Create Goods Received Note</h2>
                <button @click="openModal=false" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('grns.store') }}" class="overflow-y-auto max-h-[calc(90vh-120px)]">
                @csrf

                <!-- Basic Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Supplier *</label>
                        <select name="supplier_id" x-model="supplierId" @change="loadPOs()" required class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Purchase Order *</label>
                        <select name="purchase_order_id" x-model="poId" @change="loadPoDetails()" required class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select PO</option>
                            <template x-for="po in filteredPos" :key="po.id">
                                <option :value="po.id" x-text="`${po.po_number} - ${po.items.length} items`"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Received Date *</label>
                        <input type="date" name="received_date" x-model="receivedDate" required class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- PO Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6 text-xs sm:text-sm" x-show="poDetails">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <p class="text-gray-600 font-semibold">PO Number</p>
                            <p class="font-bold text-blue-700" x-text="poDetails && poDetails.po_number ? poDetails.po_number : 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-semibold">PO Date</p>
                            <p class="font-bold text-blue-700" x-text="poDetails && poDetails.po_date ? poDetails.po_date : 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-semibold">PO Status</p>
                            <p class="font-bold text-blue-700" x-text="poDetails && poDetails.status ? poDetails.status : 'N/A'"></p>
                        </div>
                    </div>
                </div>

                <!-- GRN Items -->
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Items Received</h3>
                    <div class="space-y-3 sm:space-y-4 border border-gray-200 rounded-lg p-3 sm:p-4 bg-gray-50 overflow-x-auto">
                        <template x-for="(item, i) in items" :key="i">
                            <div class="bg-white p-3 sm:p-4 rounded-lg border border-gray-200">
                                <!-- Hidden gas_type_id field -->
                                <input type="hidden" :name="`items[${i}][gas_type_id]`" :value="item.gas_type_id">
                                <input type="hidden" :name="`items[${i}][ordered_qty]`" :value="item.ordered_qty">

                                <!-- Gas Type Name -->
                                <div class="mb-3 sm:mb-4">
                                    <h4 class="font-bold text-sm sm:text-base" x-text="item.gas_type_name"></h4>
                                    <p class="text-xs text-gray-500" x-text="`Already: ${item.already_received} | Remaining: ${item.remaining}`"></p>
                                </div>

                                <!-- Quantities Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 sm:gap-3 mb-3 sm:mb-4">
                                    <!-- Ordered Qty -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Ordered</label>
                                        <div class="p-2 bg-gray-100 rounded border border-gray-300 text-xs sm:text-sm font-bold">
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
                                            class="w-full border border-gray-300 p-2 rounded text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Damaged Qty -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Damaged</label>
                                        <input type="number" 
                                            :name="`items[${i}][damaged_qty]`"
                                            x-model.number="item.damaged_qty"
                                            min="0"
                                            class="w-full border border-gray-300 p-2 rounded text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Rejected Qty -->
                                    <div class="hidden sm:block">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Rejected</label>
                                        <input type="number" 
                                            :name="`items[${i}][rejected_qty]`"
                                            x-model.number="item.rejected_qty"
                                            min="0"
                                            class="w-full border border-gray-300 p-2 rounded text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Net Received -->
                                    <div class="hidden lg:block">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Net Received</label>
                                        <div class="p-2 bg-green-100 rounded border border-green-300 text-xs font-bold text-green-700">
                                            <span x-text="(item.received_qty - (item.rejected_qty || 0))"></span>
                                        </div>
                                    </div>

                                    <!-- Variance -->
                                    <div class="hidden lg:block">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Variance</label>
                                        <div class="p-2 rounded border text-xs font-bold" 
                                            :class="(item.received_qty - item.ordered_qty) < 0 ? 'bg-red-100 text-red-700 border-red-300' : 'bg-orange-100 text-orange-700 border-orange-300'">
                                            <span x-text="item.received_qty - item.ordered_qty"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile Rejected Qty -->
                                <div class="sm:hidden mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Rejected</label>
                                    <input type="number" 
                                        :name="`items[${i}][rejected_qty]`"
                                        x-model.number="item.rejected_qty"
                                        min="0"
                                        class="w-full border border-gray-300 p-2 rounded text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Rejection Notes -->
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Quality Notes</label>
                                    <textarea 
                                        :name="`items[${i}][rejection_notes]`"
                                        x-model="item.rejection_notes"
                                        rows="2"
                                        class="w-full border border-gray-300 p-2 rounded text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Describe any issues...">
                                    </textarea>
                                </div>

                                <!-- Alerts -->
                                <div class="space-y-1 text-xs">
                                    <template x-if="(item.received_qty - item.ordered_qty) < 0">
                                        <div class="bg-red-50 border border-red-200 text-red-700 p-2 rounded">
                                            ⚠️ Short: <span x-text="Math.abs(item.received_qty - item.ordered_qty)"></span> units
                                        </div>
                                    </template>
                                    <template x-if="(item.received_qty - item.ordered_qty) > 0">
                                        <div class="bg-orange-50 border border-orange-200 text-orange-700 p-2 rounded">
                                            📊 Over: <span x-text="item.received_qty - item.ordered_qty"></span> units
                                        </div>
                                    </template>
                                    <template x-if="(item.damaged_qty + item.rejected_qty) > 0">
                                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-2 rounded">
                                            ⚠️ Issues: <span x-text="item.damaged_qty + item.rejected_qty"></span> units
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="items.length === 0">
                            <div class="text-center py-6 sm:py-8 text-gray-500 text-sm">
                                Select a PO to load items
                            </div>
                        </template>
                    </div>
                </div>

                <!-- General Notes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Variance Notes</label>
                        <textarea name="variance_notes" x-model="varianceNotes" rows="3" class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Explain any discrepancies..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Rejection Notes</label>
                        <textarea name="rejection_notes" x-model="rejectionNotes" rows="3" class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Explain rejections..."></textarea>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                    <button type="button" @click="openModal=false"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium text-sm">
                        Cancel
                    </button>

                    <button type="submit"
                        class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm">
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
                    if (data && data.id) {
                        this.poDetails = data;
                        this.items = data.items.map(item => ({
                            ...item,
                            received_qty: item.remaining,
                            damaged_qty: 0,
                            rejected_qty: 0,
                            rejection_notes: ''
                        }));
                    } else {
                        this.poDetails = null;
                        this.items = [];
                        alert('Failed to load PO details');
                    }
                })
                .catch(error => {
                    console.error('Error loading PO details:', error);
                    this.poDetails = null;
                    this.items = [];
                    alert('Error loading PO details');
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
