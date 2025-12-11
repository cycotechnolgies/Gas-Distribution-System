@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Vehicle Details</h1>
        <a href="{{ route('vehicles.index') }}" class="text-gray-600 hover:text-gray-800">← Back to List</a>
    </div>

    <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-200">

        <div class="bg-gradient-to-r from-sky-500 via-sky-500 to-sky-600 px-8 py-10 text-white relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>

            <div class="relative">
                <div class="w-16 h-16 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm mb-4">
                    <span class="text-white text-2xl font-bold">{{ strtoupper(substr($vehicle->vehicle_number, 0, 2)) }}</span>
                </div>

                <h2 class="text-3xl font-semibold">{{ $vehicle->vehicle_number }}</h2>
                <p class="text-sky-100 mt-1">{{ ucfirst($vehicle->type) }}</p>
            </div>
        </div>

        <div class="px-8 py-8 space-y-6">

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Vehicle Information</h3>
                <p class="text-gray-700"><strong>Type:</strong> {{ $vehicle->type }}</p>
                <p class="text-gray-700"><strong>Capacity:</strong> {{ $vehicle->capacity }} cylinders</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Assigned Driver</h3>
                <p class="text-gray-700">
                    @if($vehicle->driver)
                        <a href="{{ route('drivers.show', $vehicle->driver->id) }}" class="text-indigo-600 hover:underline">
                            {{ $vehicle->driver->name }}
                        </a>
                    @else
                        <span class="text-gray-500">Unassigned</span>
                    @endif
                </p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Status</h3>
                <span class="px-3 py-1 text-sm rounded-full
                    @if($vehicle->status === 'available') bg-green-100 text-green-700
                    @elseif($vehicle->status === 'maintenance') bg-amber-100 text-amber-700
                    @else bg-blue-100 text-blue-700 @endif">
                    {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                </span>
            </div>

            @if($vehicle->notes)
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Notes</h3>
                <p class="text-gray-700">{{ $vehicle->notes }}</p>
            </div>
            @endif

        </div>

        <div class="px-8 py-6 bg-gray-50 border-t flex justify-end gap-3">
            <a href="{{ route('vehicles.edit', $vehicle) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-lg shadow">
                Edit
            </a>

            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                  onsubmit="return confirm('Delete this vehicle? This cannot be undone.')">
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
