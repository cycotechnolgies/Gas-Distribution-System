<script>
function poForm(rates) {
    return {
        openModal: false,
        supplierId: '',
        orderDate: new Date().toISOString().split('T')[0],
        deliveryDate: '',
        notes: '',
        items: [],
        rates: rates,

        addItem() {
            this.items.push({gas_type_id:'', quantity:1, unit_price:0});
        },

        removeItem(i) {
            this.items.splice(i,1);
        },

        total() {
            return this.items.reduce((sum, i) => sum + (i.quantity * i.unit_price), 0);
        },

        setPrice(index) {
            let item = this.items[index];

            if(!this.supplierId || !item.gas_type_id) return;

            let found = this.rates.find(r =>
                r.supplier_id == this.supplierId &&
                r.gas_type_id == item.gas_type_id
            );

            item.unit_price = found ? found.rate : 0;
        },

        init() {
            this.$watch('supplierId', () => {
                this.items.forEach((item, i) => {
                    if(item.gas_type_id) {
                        this.setPrice(i);
                    }
                });
            });
        }
    }
}
</script>

@extends('layouts.app')

@section('content')
<div x-data="poForm({{ $rates->toJson() }})" x-init="init()" class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold">Purchase Orders</h1>
            <p class="text-gray-600 mt-2">Manage gas refill purchase orders</p>
        </div>
        <button @click="openModal=true; items=[]"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create PO
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

    <!-- Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-4 font-semibold">PO No</th>
                    <th class="p-4 font-semibold">Supplier</th>
                    <th class="p-4 font-semibold">Order Date</th>
                    <th class="p-4 font-semibold">Delivery Date</th>
                    <th class="p-4 font-semibold">Items</th>
                    <th class="p-4 font-semibold">Total</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $o)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-semibold text-blue-600">{{ $o->po_number }}</td>
                    <td class="p-4">{{ $o->supplier->name }}</td>
                    <td class="p-4">{{ $o->order_date->format('d M Y') }}</td>
                    <td class="p-4">
                        {{ $o->delivery_date ? $o->delivery_date->format('d M Y') : '-' }}
                    </td>
                    <td class="p-4">{{ $o->items->count() }} item(s)</td>
                    <td class="p-4 font-bold">Rs. {{ number_format($o->total_amount, 2) }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $o->status == 'Approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $o->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $o->status == 'Completed' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ $o->status }}
                        </span>
                    </td>
                    <td class="p-4 flex gap-2">
                        <form method="POST" action="{{ route('purchase-orders.status', [$o->id, 'Approved']) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                {{ $o->status != 'Pending' ? 'disabled' : '' }}
                                class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm hover:bg-green-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('purchase-orders.destroy',$o->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" 
                                onclick="return confirm('Delete this PO?')"
                                class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm hover:bg-red-200">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-6 text-center text-gray-500">
                        No purchase orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $orders->links() }}
    </div>

    <!-- Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-4xl p-8 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6">Create Purchase Order</h2>

            <form method="POST" action="{{ route('purchase-orders.store') }}">
                @csrf

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                        <select name="supplier_id" x-model="supplierId" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Date *</label>
                        <input type="date" name="order_date" x-model="orderDate" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                        <input type="date" name="delivery_date" x-model="deliveryDate" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" x-model="notes" rows="2" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add any special instructions..."></textarea>
                </div>

                <!-- Items -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Order Items</h3>
                        <button type="button" @click="addItem()"
                            class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded text-sm font-medium">
                            + Add Item
                        </button>
                    </div>

                    <div class="space-y-3 border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <template x-for="(item, i) in items" :key="i">
                            <div class="grid grid-cols-5 gap-3 items-end">
                                <!-- Gas Type -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Gas Type</label>
                                    <select 
                                        :name="`items[${i}][gas_type_id]`" 
                                        x-model="item.gas_type_id"
                                        @change="setPrice(i)"
                                        required
                                        class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select</option>
                                        @foreach($gasTypes as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                                    <input type="number" 
                                        :name="`items[${i}][quantity]`"
                                        x-model.number="item.quantity" 
                                        min="1"
                                        required
                                        class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Unit Price -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price</label>
                                    <input type="number" 
                                        step="0.01"
                                        :name="`items[${i}][unit_price]`"
                                        x-model.number="item.unit_price"
                                        required
                                        class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Line Total -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Total</label>
                                    <div class="p-2 bg-white rounded border border-gray-300 text-sm font-semibold">
                                        Rs. <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                    </div>
                                </div>

                                <!-- Remove -->
                                <div>
                                    <button type="button" @click="removeItem(i)"
                                        class="w-full bg-red-100 text-red-700 rounded p-2 text-sm hover:bg-red-200">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-if="items.length === 0">
                            <div class="text-center py-8 text-gray-500">
                                Click "Add Item" to start adding items to the order
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="text-right mb-6 border-t pt-4">
                    <div class="text-xl font-bold">
                        Grand Total: Rs. <span x-text="total().toFixed(2)"></span>
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
                        Create PO
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
