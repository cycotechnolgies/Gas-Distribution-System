@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Orders</a>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Create New Order</h1>
            <p class="text-gray-600 mt-2">Create an order for a customer with cylinder selection and pricing</p>
        </div>

        <!-- Form -->
        <form action="{{ route('orders.store') }}" method="POST" x-data="orderForm()" class="space-y-6">
            @csrf

            <!-- Customer & Route Selection -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Selection -->
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer <span class="text-red-600">*</span></label>
                        <select name="customer_id" id="customer_id" @change="updateCustomerInfo()" x-model="form.customer_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-credit="{{ $customer->getCreditAvailable() }}">
                                    {{ $customer->name }} ({{ $customer->customer_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Date -->
                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Order Date <span class="text-red-600">*</span></label>
                        <input type="date" name="order_date" id="order_date" x-model="form.order_date" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('order_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Delivery Route -->
                    <div>
                        <label for="delivery_route_id" class="block text-sm font-medium text-gray-700 mb-2">Delivery Route (Optional)</label>
                        <select name="delivery_route_id" id="delivery_route_id" x-model="form.delivery_route_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- No Route Assigned --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                            @endforeach
                        </select>
                        @error('delivery_route_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Urgent Flag -->
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_urgent" id="is_urgent" x-model="form.is_urgent"
                                class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-red-500">
                            <span class="text-sm font-medium text-gray-700">🔴 Mark as Urgent</span>
                        </label>
                    </div>
                </div>

                <!-- Customer Credit Info -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg" x-show="form.customer_id">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Credit Limit</p>
                            <p class="text-lg font-bold text-gray-900">LKR <span x-text="customerCredit.credit_limit.toFixed(2)">0.00</span></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Outstanding Balance</p>
                            <p class="text-lg font-bold text-gray-900">LKR <span x-text="customerCredit.outstanding_balance.toFixed(2)">0.00</span></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Available Credit</p>
                            <p class="text-lg font-bold" :class="customerCredit.is_over_credit ? 'text-red-600' : 'text-green-600'">
                                LKR <span x-text="customerCredit.available_credit.toFixed(2)">0.00</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                    <textarea name="notes" id="notes" x-model="form.notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Special instructions or delivery notes..."></textarea>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Cylinder Orders</h2>
                    <button type="button" @click="addItem()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Add Cylinder Type
                    </button>
                </div>

                <template x-if="form.items.length > 0">
                    <div class="space-y-4">
                        <template x-for="(item, index) in form.items" :key="index">
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <!-- Gas Type -->
                                    <div>
                                        <label :for="`gas_type_id_${index}`" class="block text-sm font-medium text-gray-700 mb-2">Gas Type <span class="text-red-600">*</span></label>
                                        <select :id="`gas_type_id_${index}`" :name="`items[${index}][gas_type_id]`" 
                                            x-model="item.gas_type_id" @change="updateItemPrice(index)" required
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Select Type --</option>
                                            @foreach($gasTypes as $gasType)
                                                <option value="{{ $gasType->id }}">{{ $gasType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div>
                                        <label :for="`quantity_${index}`" class="block text-sm font-medium text-gray-700 mb-2">Quantity <span class="text-red-600">*</span></label>
                                        <input type="number" min="1" :id="`quantity_${index}`" :name="`items[${index}][quantity]`"
                                            x-model.number="item.quantity" @change="calculateTotal()" required
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Unit Price -->
                                    <div>
                                        <label :for="`unit_price_${index}`" class="block text-sm font-medium text-gray-700 mb-2">Unit Price (LKR) <span class="text-red-600">*</span></label>
                                        <input type="number" step="0.01" :id="`unit_price_${index}`" :name="`items[${index}][unit_price]`"
                                            x-model.number="item.unit_price" @change="calculateTotal()" required
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Line Total -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Line Total</label>
                                        <div class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-900 font-semibold">
                                            LKR <span x-text="(item.quantity * item.unit_price).toFixed(2)">0.00</span>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="flex items-end">
                                        <button type="button" @click="removeItem(index)"
                                            class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mt-3">
                                    <label :for="`notes_${index}`" class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                                    <input type="text" :id="`notes_${index}`" :name="`items[${index}][notes]`"
                                        x-model="item.notes" placeholder="Item-specific notes..."
                                        class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="form.items.length === 0">
                    <div class="text-center py-8 text-gray-500">
                        <p>No items added yet. Click "Add Cylinder Type" to start.</p>
                    </div>
                </template>
            </div>

            <!-- Order Total -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Order Summary</h3>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 uppercase font-semibold">Total Items</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="form.items.length">0</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 uppercase font-semibold">Total Cylinders</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="getTotalQuantity()">0</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 uppercase font-semibold">Order Total</p>
                            <p class="text-3xl font-bold text-blue-600">LKR <span x-text="form.order_total.toFixed(2)">0.00</span></p>
                        </div>
                    </div>

                    <!-- Credit Check -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Available Credit:</p>
                                <p class="font-bold" :class="(customerCredit.available_credit - form.order_total) >= 0 ? 'text-green-600' : 'text-red-600'">
                                    LKR <span x-text="(customerCredit.available_credit - form.order_total).toFixed(2)">0.00</span>
                                </p>
                            </div>
                            <div x-show="(customerCredit.available_credit - form.order_total) < 0" class="bg-red-50 border border-red-200 rounded p-2 col-span-2">
                                <p class="text-red-700 text-xs"><strong>⚠️ Warning:</strong> Order exceeds available credit!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('orders.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                    :disabled="form.items.length === 0 || !form.customer_id">
                    Create Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function orderForm() {
    return {
        form: {
            customer_id: '',
            order_date: new Date().toISOString().split('T')[0],
            delivery_route_id: '',
            is_urgent: false,
            notes: '',
            items: [],
            order_total: 0
        },
        customerCredit: {
            credit_limit: 0,
            outstanding_balance: 0,
            available_credit: 0,
            is_over_credit: false
        },

        addItem() {
            this.form.items.push({
                gas_type_id: '',
                quantity: 1,
                unit_price: 0,
                notes: ''
            });
        },

        removeItem(index) {
            this.form.items.splice(index, 1);
            this.calculateTotal();
        },

        updateItemPrice(index) {
            // Find selected gas type and load its default price
            const select = document.querySelector(`select[name="items[${index}][gas_type_id]"]`);
            // Could set default price here if needed
            this.calculateTotal();
        },

        calculateTotal() {
            this.form.order_total = this.form.items.reduce((sum, item) => {
                return sum + (item.quantity * item.unit_price);
            }, 0);
        },

        getTotalQuantity() {
            return this.form.items.reduce((sum, item) => sum + (item.quantity || 0), 0);
        },

        updateCustomerInfo() {
            if (!this.form.customer_id) return;
            
            // Make API call to get customer credit info
            fetch(`/api/orders/customer-credit?customer_id=${this.form.customer_id}`)
                .then(r => r.json())
                .then(data => {
                    this.customerCredit = data;
                })
                .catch(e => console.error('Failed to load customer credit:', e));
        }
    }
}
</script>
@endsection
