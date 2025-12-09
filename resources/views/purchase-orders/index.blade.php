<script>
function poForm(rates) {
    return {
        openModal: false,
        supplierId: '',
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
<div x-data="poForm({{ $rates->toJson() }})" class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold">Purchase Orders</h1>
        <button @click="openModal=true; items=[]"
            class="bg-blue-600 text-white px-6 py-3 rounded-xl shadow font-semibold">
            + Create PO
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">PO No</th>
                    <th class="p-4">Supplier</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $o)
                <tr class="border-t">
                    <td class="p-4 font-semibold">{{ $o->po_number }}</td>
                    <td class="p-4">{{ $o->supplier->name }}</td>
                    <td class="p-4">{{ $o->order_date }}</td>
                    <td class="p-4">Rs. {{ $o->total_amount }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs
                            {{ $o->status == 'Approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $o->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            {{ $o->status }}
                        </span>
                    </td>
                    <td class="p-4 flex gap-2">
                        <form method="POST" action="{{ route('purchase-orders.destroy',$o->id) }}">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this PO?')"
                                class="bg-red-100 text-red-700 px-3 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-4xl p-8 rounded-xl shadow-2xl">

            <h2 class="text-2xl font-bold mb-4">Create Purchase Order</h2>

            <form method="POST" action="{{ route('purchase-orders.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <select name="supplier_id" x-model="supplierId" required class="border p-3 rounded">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="order_date" required class="border p-3 rounded">
                </div>

                <!-- Items -->
                <div class="space-y-3 mb-6">
                    <template x-for="(item, i) in items" :key="i">
                        <div class="grid grid-cols-4 gap-3">

                            <!-- Gas Type -->
                            <select 
                                :name="`items[${i}][gas_type_id]`" 
                                x-model="item.gas_type_id"
                                @change="setPrice(i)"
                                class="border p-2 rounded">
                                <option value="">Select Gas Type</option>
                                @foreach($gasTypes as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>

                            <!-- Quantity -->
                            <input type="number" min="1" 
                                :name="`items[${i}][quantity]`"
                                x-model="item.quantity" 
                                class="border p-2 rounded">

                            <!-- Unit Price -->
                            <input type="number" step="0.01"
                                :name="`items[${i}][unit_price]`"
                                x-model="item.unit_price"
                                class="border p-2 rounded">

                            <!-- Remove -->
                            <button type="button" @click="removeItem(i)"
                                class="bg-red-100 text-red-700 rounded px-2">X</button>

                        </div>
                    </template>

                    <button type="button" @click="addItem()"
                        class="bg-gray-100 px-4 py-2 rounded">+ Add Item</button>
                </div>

                <!-- Total -->
                <div class="text-right font-bold text-lg mb-6">
                    Total: Rs. <span x-text="total().toFixed(2)"></span>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button type="button" @click="openModal=false"
                        class="px-4 py-2 bg-gray-200 rounded">Cancel</button>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded font-semibold">
                        Save PO
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
