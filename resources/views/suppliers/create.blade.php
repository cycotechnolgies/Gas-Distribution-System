@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Add Supplier</h1>

<form action="{{ route('suppliers.store') }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf
    <div class="mb-4">
        <label class="block mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name') }}"
               class="w-full border px-3 py-2 rounded">
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label class="block mb-1">Address</label>
        <textarea name="address" class="w-full border px-3 py-2 rounded">{{ old('address') }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}"
               class="w-full border px-3 py-2 rounded">
    </div>

    <div class="mb-4">
        <label class="block mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}"
               class="w-full border px-3 py-2 rounded">
    </div>

    <div class="flex space-x-2">
        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded">
            Save
        </button>
        <a href="{{ route('suppliers.index') }}" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">
            Cancel
        </a>
    </div>
</form>
@endsection
