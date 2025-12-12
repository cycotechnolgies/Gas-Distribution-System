@extends('layouts.app')

@section('content')
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

<div x-data="poForm({{ $rates->toJson() }})" x-init="init()" class="space-y-4 sm:space-y-6 lg:space-y-8 px-2 sm:px-4">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-4xl font-bold">Purchase Orders</h1>
            <p class="text-gray-600 text-xs sm:text-sm lg:text-base mt-2">Manage gas refill purchase orders</p>
        </div>
        <button @click="openModal=true; items=[]"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 lg:px-6 py-2 sm:py-2 lg:py-3 rounded-lg sm:rounded-xl shadow-lg font-semibold flex items-center gap-2 text-xs sm:text-sm lg:text-base whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span class="hidden sm:inline">Create PO</span><span class="sm:hidden">Add</span>
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

    <!-- Table - Desktop View -->
    <div class="hidden md:block bg-white rounded-lg sm:rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm lg:text-base">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-3 lg:p-4 font-semibold text-left">PO No</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Supplier</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Order Date</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Delivery Date</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Items</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Total</th>
                    <th class="p-3 lg:p-4 font-semibold text-left">Status</th>
                    <th class="p-3 lg:p-4 font-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $o)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 lg:p-4 font-semibold text-blue-600">{{ $o->po_number }}</td>
                    <td class="p-3 lg:p-4">{{ $o->supplier->name }}</td>
                    <td class="p-3 lg:p-4">{{ $o->order_date->format('d M Y') }}</td>
                    <td class="p-3 lg:p-4">
                        {{ $o->delivery_date ? $o->delivery_date->format('d M Y') : '-' }}
                    </td>
                    <td class="p-3 lg:p-4">{{ $o->items->count() }} item(s)</td>
                    <td class="p-3 lg:p-4 font-bold">LKR {{ number_format($o->total_amount, 2) }}</td>
                    <td class="p-3 lg:p-4">
                        <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-semibold
                            {{ $o->status == 'Approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $o->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $o->status == 'Completed' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ $o->status }}
                        </span>
                    </td>
                    <td class="p-3 lg:p-4 flex gap-2 justify-center flex-wrap">
                        <form method="POST" action="{{ route('purchase-orders.status', [$o->id, 'Approved']) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                {{ $o->status != 'Pending' ? 'disabled' : '' }}
                                class="bg-green-100 text-green-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm hover:bg-green-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                Approve
                            </button>
                        </form>
                        <a href="{{ route('purchase-orders.show', $o->id) }}" class="bg-indigo-100 text-indigo-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm hover:bg-indigo-200">
                            View
                        </a>
                        <form method="POST" action="{{ route('purchase-orders.destroy',$o->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" 
                                onclick="return confirm('Delete this PO?')"
                                class="bg-red-100 text-red-700 px-2 lg:px-3 py-1 rounded text-xs lg:text-sm hover:bg-red-200">
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

    <!-- Card View - Mobile -->
    <div class="md:hidden space-y-3 sm:space-y-4">
        @forelse($orders as $o)
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
                <div class="flex justify-between items-start gap-2 mb-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">PO Number</p>
                        <p class="text-base sm:text-lg font-bold text-blue-600">{{ $o->po_number }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap
                        {{ $o->status == 'Approved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $o->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $o->status == 'Completed' ? 'bg-blue-100 text-blue-700' : '' }}">
                        {{ $o->status }}
                    </span>
                </div>

                <div class="space-y-2 mb-3 pb-3 border-b border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">Supplier:</span>
                        <span class="font-medium">{{ $o->supplier->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">Order Date:</span>
                        <span class="font-medium">{{ $o->order_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">Delivery:</span>
                        <span class="font-medium">{{ $o->delivery_date ? $o->delivery_date->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-semibold">Items:</span>
                        <span class="font-medium">{{ $o->items->count() }}</span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span class="text-gray-700 font-semibold">Total:</span>
                        <span class="font-bold text-indigo-600">LKR {{ number_format($o->total_amount, 2) }}</span>
                    </div>
                </div>

                <div class="flex gap-2 flex-wrap">
                    <form method="POST" action="{{ route('purchase-orders.status', [$o->id, 'Approved']) }}" class="flex-1 min-w-[80px]">
                        @csrf
                        <button type="submit" 
                            {{ $o->status != 'Pending' ? 'disabled' : '' }}
                            class="w-full bg-green-100 text-green-700 px-2 py-1.5 rounded text-xs font-semibold hover:bg-green-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            Approve
                        </button>
                    </form>
                    <a href="{{ route('purchase-orders.show', $o->id) }}" class="flex-1 min-w-[80px] bg-indigo-100 text-indigo-700 px-2 py-1.5 rounded text-xs font-semibold hover:bg-indigo-200 text-center">
                        View
                    </a>
                    <form method="POST" action="{{ route('purchase-orders.destroy',$o->id) }}" class="flex-1 min-w-[80px]">
                        @csrf @method('DELETE')
                        <button type="submit" 
                            onclick="return confirm('Delete this PO?')"
                            class="w-full bg-red-100 text-red-700 px-2 py-1.5 rounded text-xs font-semibold hover:bg-red-200">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg p-8 text-center">
                <p class="text-gray-500">No purchase orders found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        {{ $orders->links() }}
    </div>

    <!-- Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-2 sm:p-4 z-50 overflow-y-auto">
        <div class="bg-white w-full max-w-2xl lg:max-w-4xl p-4 sm:p-6 lg:p-8 rounded-lg sm:rounded-xl shadow-2xl my-auto">

            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold">Create Purchase Order</h2>
                <button @click="openModal=false" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('purchase-orders.store') }}" class="overflow-y-auto max-h-[calc(90vh-120px)]">
                @csrf

                <!-- Basic Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Supplier *</label>
                        <select name="supplier_id" x-model="supplierId" required class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Order Date *</label>
                        <input type="date" name="order_date" x-model="orderDate" required class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Delivery Date</label>
                        <input type="date" name="delivery_date" x-model="deliveryDate" class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Notes</label>
                    <textarea name="notes" x-model="notes" rows="2" class="w-full border border-gray-300 p-2 sm:p-3 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Add any special instructions..."></textarea>
                </div>

                <!-- Items -->
                <div class="mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 mb-3 sm:mb-4">
                        <h3 class="text-base sm:text-lg font-semibold">Order Items</h3>
                        <button type="button" @click="addItem()"
                            class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 px-3 sm:px-4 py-2 rounded text-xs sm:text-sm font-medium">
                            + Add Item
                        </button>
                    </div>

                    <div class="space-y-3 sm:space-y-4 border border-gray-200 rounded-lg p-3 sm:p-4 bg-gray-50 overflow-x-auto">
                        <template x-for="(item, i) in items" :key="i">
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3 items-end bg-white p-3 rounded border border-gray-200">
                                <!-- Gas Type -->
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Gas Type</label>
                                    <select 
                                        :name="`items[${i}][gas_type_id]`" 
                                        x-model="item.gas_type_id"
                                        @change="setPrice(i)"
                                        required
                                        class="w-full border border-gray-300 p-2 rounded text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                        class="w-full border border-gray-300 p-2 rounded text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Unit Price -->
                                <div class="hidden sm:block">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price</label>
                                    <input type="number" 
                                        step="0.01"
                                        :name="`items[${i}][unit_price]`"
                                        x-model.number="item.unit_price"
                                        required
                                        class="w-full border border-gray-300 p-2 rounded text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Line Total -->
                                <div class="hidden sm:block">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Total</label>
                                    <div class="p-2 bg-blue-50 rounded border border-gray-300 text-xs sm:text-sm font-semibold text-blue-700">
                                        <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                    </div>
                                </div>

                                <!-- Mobile Unit Price & Total -->
                                <div class="sm:hidden col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Price & Total</label>
                                    <input type="number" 
                                        step="0.01"
                                        :name="`items[${i}][unit_price]`"
                                        x-model.number="item.unit_price"
                                        required
                                        placeholder="Price"
                                        class="w-full border border-gray-300 p-2 rounded text-xs mb-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <div class="p-2 bg-blue-50 rounded border border-gray-300 text-xs font-semibold text-blue-700 text-center">
                                        <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span>
                                    </div>
                                </div>

                                <!-- Remove -->
                                <div class="hidden sm:block">
                                    <button type="button" @click="removeItem(i)"
                                        class="w-full bg-red-100 text-red-700 rounded p-2 text-xs hover:bg-red-200 font-semibold">
                                        Remove
                                    </button>
                                </div>

                                <!-- Remove Mobile -->
                                <div class="sm:hidden col-span-2">
                                    <button type="button" @click="removeItem(i)"
                                        class="w-full bg-red-100 text-red-700 rounded p-2 text-xs hover:bg-red-200 font-semibold">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </template>

                        <template x-if="items.length === 0">
                            <div class="text-center py-6 sm:py-8 text-gray-500 text-sm">
                                Click "Add Item" to start adding items to the order
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="text-right mb-4 sm:mb-6 border-t pt-3 sm:pt-4">
                    <div class="text-lg sm:text-xl font-bold text-gray-900">
                        Grand Total: <span class="text-indigo-600" x-text="'LKR ' + total().toFixed(2)"></span>
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
                        Create PO
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
