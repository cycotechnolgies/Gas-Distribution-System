@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Driver Details</h1>
        <a href="{{ route('drivers.index') }}" class="text-gray-600 hover:text-gray-800">← Back to List</a>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-200">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-indigo-500 via-indigo-500 to-indigo-600 px-8 py-10 text-white relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>

            <div class="relative">
                <div class="w-16 h-16 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm mb-4">
                    <span class="text-white text-2xl font-bold">{{ strtoupper(substr($driver->name, 0, 2)) }}</span>
                </div>

                <h2 class="text-3xl font-semibold">{{ $driver->name }}</h2>
                <p class="text-indigo-100 mt-1">Driver ID #{{ $driver->id }}</p>
            </div>
        </div>

        <!-- Details -->
        <div class="px-8 py-8 space-y-6">

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Contact Information</h3>
                <p class="text-gray-700"><strong>Phone:</strong> {{ $driver->phone ?? '—' }}</p>
                <p class="text-gray-700"><strong>Address:</strong> {{ $driver->address ?? '—' }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Driver License</h3>
                <p class="text-gray-700"><strong>License No:</strong> {{ $driver->license_number ?? '—' }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Status</h3>
                <span class="px-3 py-1 text-sm rounded-full 
                    {{ $driver->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ ucfirst($driver->status) }}
                </span>
            </div>

            @if($driver->notes)
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Notes</h3>
                <p class="text-gray-700">{{ $driver->notes }}</p>
            </div>
            @endif

        </div>

        <!-- Footer Actions -->
        <div class="px-8 py-6 bg-gray-50 border-t flex justify-end gap-3">
            <a href="{{ route('drivers.edit', $driver) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-lg shadow">
                Edit
            </a>

            <form action="{{ route('drivers.destroy', $driver) }}" method="POST"
                  onsubmit="return confirm('Delete this driver? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">
                    Delete
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
