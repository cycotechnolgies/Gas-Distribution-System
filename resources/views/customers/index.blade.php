@extends('layouts.app')

@section('content')

<div 
    x-data="{
        openModal:false,
        customer:{ id:null, name:'', phone:'', email:'', address:'', nic:'', city:'', customer_type:'retail', credit_limit:0 }
    }"
    class="space-y-8 pb-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold text-gray-900">Customers</h1>
        <button 
            @click="customer={id:null, name:'', phone:'', email:'', address:'', nic:'', city:'', customer_type:'retail', credit_limit:0}; openModal=true"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow">
            + Add Customer
        </button>
    </div>

    <hr>

    <!-- Success -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded">{{ session('success') }}</div>
    @endif

    <!-- Customers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($customers as $c)
        <div 
            class="bg-white shadow rounded-xl p-5 border"
            x-data="{ data:{ id:{{ $c->id }}, name:'{{ $c->name }}', phone:'{{ $c->phone }}', email:'{{ $c->email }}', address:'{{ $c->address }}', nic:'{{ $c->nic }}', city:'{{ $c->city }}', customer_type:'{{ $c->customer_type }}', credit_limit:{{ $c->credit_limit }} } }">

            <div class="border-b pb-3 mb-3">
                <h3 class="text-xl font-semibold">{{ $c->name }}</h3>
                <p class="text-gray-600 text-sm">{{ $c->city }}</p>
            </div>

            <div class="text-sm text-gray-700 space-y-1">
                <p><strong>Phone:</strong> {{ $c->phone }}</p>
                <p><strong>Email:</strong> {{ $c->email }}</p>
                <p><strong>Type:</strong> {{ ucfirst($c->customer_type) }}</p>
            </div>

            <div class="flex gap-2 mt-4">
                <a 
                    href="{{ route('customers.show', $c->id) }}"
                    class="flex-1 bg-blue-100 text-blue-700 py-2 rounded text-center text-sm">
                    View
                </a>

                <button 
                    @click="customer = data; openModal = true"
                    class="flex-1 bg-amber-100 text-amber-700 py-2 rounded text-sm">
                    Edit
                </button>

                <form method="POST" action="{{ route('customers.destroy', $c->id) }}" class="flex-1">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete customer?')"
                        class="w-full bg-red-100 text-red-700 py-2 rounded text-sm">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $customers->links() }}</div>

    <!-- Modal -->
    <div 
        x-show="openModal" 
        class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
        x-cloak>

        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-8">

            <h2 class="text-2xl font-bold mb-4" 
                x-text="customer.id ? 'Edit Customer' : 'Add Customer'">
            </h2>

            <form :action="customer.id ? `/customers/${customer.id}` : '/customers'" method="POST">
                @csrf
                <template x-if="customer.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input name="name" label="Name" x-model="customer.name" required />
                    <x-form.input name="phone" label="Phone" x-model="customer.phone" />
                    <x-form.input name="email" label="Email" type="email" x-model="customer.email" />
                    <x-form.input name="nic" label="NIC" x-model="customer.nic" />
                    <x-form.input name="city" label="City" x-model="customer.city" />
                    <x-form.input name="address" label="Address" x-model="customer.address"/>
                </div>


                <div class="grid grid-cols-2 gap-4 mt-4">
                    <select 
                        name="customer_type"
                        x-model="customer.customer_type"
                        class="border p-2 rounded w-full">
                        <option value="retail">Retail</option>
                        <option value="wholesale">Wholesale</option>
                    </select>

                    <x-form.input name="credit_limit" type="number" label="Credit Limit" x-model="customer.credit_limit" />
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openModal=false" class="px-4 py-2 bg-gray-200 rounded">
                        Cancel
                    </button>
                    <button class="px-6 py-2 bg-blue-600 text-white rounded">Save</button>
                </div>

            </form>
        </div>
    </div>

</div>

@endsection
