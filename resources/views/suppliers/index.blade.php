@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Suppliers</h1>
    <a href="{{ route('suppliers.create') }}"
       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add Supplier</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white shadow rounded">
    <table class="min-w-full table-auto">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Address</th>
                <th class="px-4 py-2">Phone</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            <tr class="border-b">
                <td class="px-4 py-2">{{ $supplier->name }}</td>
                <td class="px-4 py-2">{{ $supplier->address }}</td>
                <td class="px-4 py-2">{{ $supplier->phone }}</td>
                <td class="px-4 py-2">{{ $supplier->email }}</td>
                <td class="px-4 py-2 flex space-x-2">
                    <a href="{{ route('suppliers.edit', $supplier->id) }}"
                       class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"
                                onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-4">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
