@extends('layouts.app')

@section('title', 'Route ' . $deliveryRoute->route_name)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('delivery-routes.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Routes</a>
            <div class="flex justify-between items-start mt-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">{{ $deliveryRoute->route_name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $deliveryRoute->route_date->format('l, d F Y') }}</p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-bold
                    {{ $deliveryRoute->route_status === 'Planned' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $deliveryRoute->route_status === 'InProgress' ? 'bg-purple-100 text-purple-800' : '' }}
                    {{ $deliveryRoute->route_status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $deliveryRoute->route_status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ $deliveryRoute->route_status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Route Information -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Route Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Driver</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $deliveryRoute->driver?->name ?? 'Not assigned' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Assistant</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $deliveryRoute->assistant?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Vehicle</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $deliveryRoute->vehicle?->vehicle_number ?? '-' }}</p>
                        </div>
                    </div>

                    @if($deliveryRoute->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-600 uppercase font-semibold">Notes</p>
                            <p class="text-gray-900 mt-2">{{ $deliveryRoute->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Customer Stops Table -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Delivery Stops</h2>

                    @if($deliveryRoute->stops->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">#</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">Customer</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">Order</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">Planned Time</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">Actual Time</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($deliveryRoute->stops as $stop)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $stop->stop_order }}</td>
                                            <td class="px-4 py-3 text-gray-900">{{ $stop->customer->name }}</td>
                                            <td class="px-4 py-3 text-gray-900">{{ $stop->order?->order_number ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-900">{{ $stop->getPlannedTimeFormatted() }}</td>
                                            <td class="px-4 py-3 text-gray-900">{{ $stop->getActualTimeFormatted() }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    {{ $stop->isCompleted() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $stop->getDeliveryStatus() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No stops assigned to this route.</p>
                    @endif
                </div>
            </div>

            <!-- Right Sidebar: Statistics & Actions -->
            <div>
                <!-- Statistics -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Route Progress</h2>

                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-semibold text-gray-700">Completion</span>
                                <span class="text-sm font-bold text-gray-900">{{ $stats['completion_percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['completion_percentage'] }}%"></div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Stops:</span>
                                <span class="font-bold text-gray-900">{{ $stats['total_stops'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Completed:</span>
                                <span class="font-bold text-green-600">{{ $stats['completed_stops'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Pending:</span>
                                <span class="font-bold text-orange-600">{{ $stats['pending_stops'] }}</span>
                            </div>
                        </div>

                        @if($stats['duration_minutes'])
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Duration:</span>
                                    <span class="font-bold text-gray-900">{{ $stats['duration_minutes'] }} min</span>
                                </div>
                            </div>
                        @endif

                        @if($lateCount > 0)
                            <div class="border-t border-gray-200 pt-3 text-orange-600">
                                <span class="text-sm font-semibold">⚠️ {{ $lateCount }} late deliveries</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Transitions -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Actions</h2>

                    <div class="space-y-2">
                        @if($deliveryRoute->canTransitionTo('InProgress'))
                            <form action="{{ route('delivery-routes.markInProgress', $deliveryRoute) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">
                                    Start Route
                                </button>
                            </form>
                        @endif

                        @if($deliveryRoute->canTransitionTo('Completed'))
                            <form action="{{ route('delivery-routes.markCompleted', $deliveryRoute) }}" method="POST"
                                onsubmit="return confirm('All stops must be completed. Proceed?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium"
                                    {{ !$isFullyCompleted ? 'disabled' : '' }}>
                                    {{ !$isFullyCompleted ? 'Complete All Stops First' : 'Mark Complete' }}
                                </button>
                            </form>
                        @endif

                        @if($deliveryRoute->canBeDeleted())
                            <a href="{{ route('delivery-routes.edit', $deliveryRoute) }}" class="w-full block text-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm font-medium">
                                Edit Route
                            </a>

                            <form action="{{ route('delivery-routes.destroy', $deliveryRoute) }}" method="POST"
                                onsubmit="return confirm('Delete this route?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                                    Delete Route
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Timing Info -->
                @if($deliveryRoute->actual_start_time || $deliveryRoute->actual_end_time)
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Timing</h2>

                        @if($deliveryRoute->actual_start_time)
                            <div class="mb-3">
                                <p class="text-xs text-gray-600 uppercase font-semibold">Started</p>
                                <p class="text-gray-900">{{ $deliveryRoute->actual_start_time->format('d M Y H:i') }}</p>
                            </div>
                        @endif

                        @if($deliveryRoute->actual_end_time)
                            <div>
                                <p class="text-xs text-gray-600 uppercase font-semibold">Completed</p>
                                <p class="text-gray-900">{{ $deliveryRoute->actual_end_time->format('d M Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
