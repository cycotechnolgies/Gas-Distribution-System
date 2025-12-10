@extends('layouts.app')

@section('content')
<div x-data="ordersForm({{ $gasTypes->toJson() }}, {{ $stocks->toJson() }})" class="space-y-8">

    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold">Orders</h1>
        <button @click="openModal=true; resetForm()" class="bg-blue-600 text-white px-6 py-3 rounded">
            + New Order
        </button>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Order #</th>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Route</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $o)
                <tr class="border-t">
                    <td class="p-4 font-semibold">{{ $o->order_number }}</td>
                    <td class="p-4">{{ $o->customer->name }}</td>
                    <td class="p-4">{{ $o->deliveryRoute?->route_name ?? '-' }}</td>
                    <td class="p-4">{{ $o->order_date }}</td>
                    <td class="p-4">Rs. {{ number_format($o->total_amount,2) }}</td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs {{ $o->status=='Delivered' ? 'bg-green-100 text-green-700' : ($o->status=='Loaded' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $o->status }}
                        </span>
                    </td>
                    <td class="p-4 flex gap-2">
                        <a href="{{ route('orders.show', $o->id) }}" class="bg-gray-100 px-3 py-1 rounded">View</a>

                        <!-- Change Status Dropdown -->
                        <form method="POST" action="{{ route('orders.status', [$o->id, 'Loaded']) }}">
                            @csrf
                            <button class="bg-indigo-100 px-3 py-1 rounded">Mark Loaded</button>
                        </form>

                        <form method="POST" action="{{ route('orders.status', [$o->id, 'Delivered']) }}">
                            @csrf
                            <button class="bg-green-100 px-3 py-1 rounded">Mark Delivered</button>
                        </form>

                        <form method="POST" action="{{ route('orders.status', [$o->id, 'Completed']) }}">
                            @csrf
                            <button class="bg-gray-200 px-3 py-1 rounded">Complete</button>
                        </form>

                        <form method="POST" action="{{ route('orders.destroy', $o->id) }}">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete order?')" class="bg-red-100 px-3 py-1 rounded">Delete</button>
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

            <h2 class="text-2xl font-bold mb-4">Create Order</h2>

            <form method="POST" action="{{ route('orders.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <select name="customer_id" x-model="form.customer_id" required class="border p-3 rounded">
                        <option value="">Select Customer</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <select name="delivery_route_id" x-model="form.delivery_route_id" class="border p-3 rounded">
                        <option value="">Select Delivery Route (optional)</option>
                        @foreach($routes as $r)
                            <option value="{{ $r->id }}">{{ $r->route_name }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="order_date" x-model="form.order_date" class="border p-3 rounded" required>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="urgent" x-model="form.urgent" class="rounded">
                        <span>Urgent</span>
                    </label>
                </div>

                <!-- Items -->
                <div class="space-y-3 mb-4">
                    <template x-for="(item, i) in form.items" :key="i">
                        <div class="grid grid-cols-5 gap-3">
                            <select :name="`items[${i}][gas_type_id]`" x-model="item.gas_type_id" @change="setPrice(i)" class="border p-2 rounded">
                                <option value="">Select Gas</option>
                                @foreach($gasTypes as $g)
                                    <option value="{{ $g->id }}" data-price="{{ $g->price }}">{{ $g->name }}</option>
                                @endforeach
                            </select>

                            <input type="number" min="1" :name="`items[${i}][quantity]`" x-model="item.quantity" class="border p-2 rounded">

                            <input type="number" step="0.01" :name="`items[${i}][unit_price]`" x-model="item.unit_price" class="border p-2 rounded">

                            <div class="flex items-center gap-2">
                                <div class="text-sm">Rs. <span x-text="(item.quantity * item.unit_price).toFixed(2)"></span></div>
                            </div>

                            <button type="button" @click="removeItem(i)" class="bg-red-100 text-red-700 rounded px-2">X</button>
                        </div>
                    </template>

                    <button type="button" @click="addItem()" class="bg-gray-100 px-4 py-2 rounded">+ Add Item</button>
                </div>

                <div class="text-right font-bold mb-4">Total: Rs. <span x-text="total().toFixed(2)"></span></div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="openModal=false" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded">Save Order</button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function ordersForm(gasTypes, stocks) {
    // gasTypes: array of {id,name,price}
    // stocks: object map gas_type_id => full_qty
    gasTypes = gasTypes || [];
    stocks = stocks || {};

    return {
        openModal: false,
        form: {
            customer_id: '',
            delivery_route_id: '',
            order_date: new Date().toISOString().slice(0,10),
            urgent: false,
            items: []
        },

        resetForm() {
            this.form = { customer_id:'', delivery_route_id:'', order_date:new Date().toISOString().slice(0,10), urgent:false, items: [] };
        },

        addItem() {
            this.form.items.push({ gas_type_id:'', quantity:1, unit_price:0 });
        },

        removeItem(i) {
            this.form.items.splice(i,1);
        },

        setPrice(i) {
            let item = this.form.items[i];
            if(!item.gas_type_id) return;
            let found = gasTypes.find(g => g.id == item.gas_type_id);
            item.unit_price = found ? parseFloat(found.price) : 0;
        },

        total() {
            return this.form.items.reduce((sum,i) => sum + (i.quantity * i.unit_price), 0);
        }
    }
}
</script>
@endsection
