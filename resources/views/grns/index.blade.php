@extends('layouts.app')

@section('content')
<div x-data="{
    openModal:false,
    items: [],
    poId: '',

    loadPO() {
        if(!this.poId) return;

        fetch('/api/po-items/' + this.poId)
            .then(res => res.json())
            .then(data => {
                this.items = data;
            });
    }
}"
 class="space-y-8">

    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold">GRN</h1>
        <button @click="openModal=true"
            class="bg-blue-600 text-white px-6 py-3 rounded-xl shadow">
            + Create GRN
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white shadow rounded-xl overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">GRN No</th>
                    <th class="p-4">Supplier</th>
                    <th class="p-4">PO</th>
                    <th class="p-4">Date</th>
                    <th class="p-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grns as $g)
                <tr class="border-t">
                    <td class="p-4 font-semibold">{{ $g->grn_number }}</td>
                    <td class="p-4">{{ $g->purchaseOrder->po_number }}</td>
                    <td class="p-4">{{ $g->received_date }}</td>
                    <td class="p-4">{{ $g->status }}</td>
                    <td class="p-4">
                        <form method="POST" action="{{ route('grns.approve', $g->id) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Approve</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-5xl p-8 rounded-xl shadow-2xl">
            <h2 class="text-2xl font-bold mb-4">Create GRN</h2>

            <form method="POST" action="{{ route('grns.store') }}">
                @csrf

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <select name="supplier_id" class="border p-3 rounded" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>

                    <select name="purchase_order_id" x-model="poId" @change="loadPO()">
                        <option value="">Select PO</option>
                        @foreach($purchaseOrders as $p)
                            <option value="{{ $p->id }}">{{ $p->po_number }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="received_date" class="border p-3 rounded" required>
                </div>

                <!-- Items -->
                <div class="space-y-3">
                    <template x-for="(item, i) in items" :key="i">
                        <div class="grid grid-cols-5 gap-3">
                            <input type="hidden" :name="`items[${i}][gas_type_id]`" x-model="item.gas_type_id">

                            <input type="text" class="border p-2 bg-gray-100" :value="item.gas_type_name" disabled>

                            <input type="number" :name="`items[${i}][ordered_qty]`" x-model="item.ordered_qty" readonly class="border p-2">

                            <input type="number" :name="`items[${i}][received_qty]`" x-model="item.received_qty" class="border p-2">

                            <input type="number" :name="`items[${i}][rejected_qty]`" x-model="item.rejected_qty" class="border p-2">
                        </div>
                    </template>


                    <button type="button" @click="addItem()"
                        class="bg-gray-100 px-4 py-2 rounded">+ Add Item</button>
                </div>

                <div class="flex justify-end mt-6 gap-3">
                    <button type="button" @click="openModal=false" class="bg-gray-200 px-4 py-2 rounded">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded font-semibold">
                        Save GRN
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
