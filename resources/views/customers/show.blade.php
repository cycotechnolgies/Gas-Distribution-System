@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <h1 class="text-4xl font-bold">{{ $customer->name }}</h1>

    <div class="bg-white rounded-xl shadow p-6 space-y-3">
        <p><strong>Phone:</strong> {{ $customer->phone }}</p>
        <p><strong>Email:</strong> {{ $customer->email }}</p>
        <p><strong>NIC:</strong> {{ $customer->nic }}</p>
        <p><strong>City:</strong> {{ $customer->city }}</p>
        <p><strong>Address:</strong> {{ $customer->address }}</p>
        <p><strong>Type:</strong> {{ ucfirst($customer->customer_type) }}</p>
        <p><strong>Credit Limit:</strong> Rs. {{ number_format($customer->credit_limit,2) }}</p>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mt-6">
        <h2 class="text-2xl font-bold mb-4">Order History</h2>

        <p class="text-gray-500">Order module not implemented yet.</p>
    </div>

</div>

@endsection
