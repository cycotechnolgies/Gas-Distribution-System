@extends('layouts.app')

@section('title', 'Edit Delivery Route')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('delivery-routes.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Routes</a>
            <h1 class="text-4xl font-bold text-gray-900 mt-4">Edit Delivery Route</h1>
        </div>

        <div class="bg-white shadow-md rounded-lg p-8">
            <form action="{{ route('delivery-routes.update', $deliveryRoute->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Route Information -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Route Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="route_name" class="block text-sm font-semibold text-gray-700 mb-2">Route Name *</label>
                            <input type="text" id="route_name" name="route_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('route_name', $deliveryRoute->route_name) }}">
                            @error('route_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="route_date" class="block text-sm font-semibold text-gray-700 mb-2">Route Date *</label>
                            <input type="date" id="route_date" name="route_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('route_date', $deliveryRoute->route_date ? $deliveryRoute->route_date->format('Y-m-d') : '') }}">
                            @error('route_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $deliveryRoute->notes) }}</textarea>
                    </div>
                </div>

                <!-- Personnel Assignment -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Personnel & Vehicle</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="driver_id" class="block text-sm font-semibold text-gray-700 mb-2">Driver *</label>
                            <select id="driver_id" name="driver_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a driver...</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ $deliveryRoute->driver_id == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            @error('driver_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="assistant_id" class="block text-sm font-semibold text-gray-700 mb-2">Assistant (Optional)</label>
                            <select id="assistant_id" name="assistant_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select an assistant...</option>
                                @foreach($assistants as $assistant)
                                    <option value="{{ $assistant->id }}" {{ $deliveryRoute->assistant_id == $assistant->id ? 'selected' : '' }}>{{ $assistant->name }}</option>
                                @endforeach
                            </select>
                            @error('assistant_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="vehicle_id" class="block text-sm font-semibold text-gray-700 mb-2">Vehicle (Optional)</label>
                            <select id="vehicle_id" name="vehicle_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a vehicle...</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ $deliveryRoute->vehicle_id == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->vehicle_number }} ({{ $vehicle->type }})</option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6 border-t">
                    <a href="{{ route('delivery-routes.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">Cancel</a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Update Route</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
