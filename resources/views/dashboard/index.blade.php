@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-gray-500">Suppliers</h3>
        <h1 class="text-3xl font-bold mt-2">12</h1>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-gray-500">Customers</h3>
        <h1 class="text-3xl font-bold mt-2">35</h1>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-gray-500">Pending Orders</h3>
        <h1 class="text-3xl font-bold mt-2">7</h1>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-gray-500">Stock Items</h3>
        <h1 class="text-3xl font-bold mt-2">24</h1>
    </div>
</div>
@endsection
