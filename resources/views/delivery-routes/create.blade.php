@extends('layouts.app')

@section('title', 'Create Delivery Route')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('delivery-routes.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Routes</a>
            <h1 class="text-4xl font-bold text-gray-900 mt-4">Create Delivery Route</h1>
        </div>

        <div class="bg-white shadow-md rounded-lg p-8">
            <form action="{{ route('delivery-routes.store') }}" method="POST" x-data="createRoute()">
                @csrf

                <!-- Route Information -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Route Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="route_name" class="block text-sm font-semibold text-gray-700 mb-2">Route Name *</label>
                            <input type="text" id="route_name" name="route_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Route A - Downtown" value="{{ old('route_name') }}">
                            @error('route_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="route_date" class="block text-sm font-semibold text-gray-700 mb-2">Route Date *</label>
                            <input type="date" id="route_date" name="route_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('route_date', date('Y-m-d')) }}">
                            @error('route_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Any special instructions...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Personnel Assignment -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Personnel & Vehicle</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="driver_id" class="block text-sm font-semibold text-gray-700 mb-2">Driver *</label>
                            <select id="driver_id" name="driver_id" required x-model="form.driver_id" @change="updateDriverAvailability()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a driver...</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            @error('driver_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="assistant_id" class="block text-sm font-semibold text-gray-700 mb-2">Assistant (Optional)</label>
                            <select id="assistant_id" name="assistant_id" x-model="form.assistant_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select an assistant...</option>
                                @foreach($assistants as $assistant)
                                    <option value="{{ $assistant->id }}">{{ $assistant->name }}</option>
                                @endforeach
                            </select>
                            @error('assistant_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="vehicle_id" class="block text-sm font-semibold text-gray-700 mb-2">Vehicle (Optional)</label>
                            <select id="vehicle_id" name="vehicle_id" x-model="form.vehicle_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a vehicle...</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} ({{ $vehicle->type }})</option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Customer Stops -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Stops</h2>

                    <div id="stops-container" class="space-y-4">
                        <template x-for="(stop, index) in form.stops" :key="index">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Stop <span x-text="index + 1"></span></h3>
                                    <button type="button" @click="removeStop(index)" class="text-red-600 hover:text-red-800 font-medium">
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Customer Selection -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Customer *</label>
                                        <select :name="`stops[${index}][customer_id]`" required
                                            @change="$el.style.borderColor = $el.value ? '#d1d5db' : '#ef4444'"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select customer...</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Order Selection -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Order (Optional)</label>
                                        <select :name="`stops[${index}][order_id]`"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select order...</option>
                                            @foreach($orders as $order)
                                                <option value="{{ $order->id }}">{{ $order->order_number }} - {{ $order->customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Planned Time -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Planned Time (Optional)</label>
                                        <input type="time" :name="`stops[${index}][planned_time]`"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <!-- Stop Order -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Stop #</label>
                                        <input type="number" :value="index + 1" disabled
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addStop()" class="mt-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium">
                        + Add Stop
                    </button>

                    <div x-show="form.stops.length === 0" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
                        At least one stop is required for the route.
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6 border-t">
                    <a href="{{ route('delivery-routes.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                        Cancel
                    </a>
                    <button type="submit" :disabled="form.stops.length === 0" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Create Route
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function createRoute() {
    return {
        form: {
            driver_id: '',
            assistant_id: '',
            vehicle_id: '',
            stops: [
                { customer_id: '', order_id: '', planned_time: '' }
            ]
        },

        addStop() {
            this.form.stops.push({
                customer_id: '',
                order_id: '',
                planned_time: ''
            });
        },

        removeStop(index) {
            this.form.stops.splice(index, 1);
        },

        updateDriverAvailability() {
            // This could be extended to fetch available drivers for the selected date
        }
    };
}
</script>
@endsection
