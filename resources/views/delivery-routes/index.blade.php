@extends('layouts.app')

@section('title', 'Delivery Routes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Delivery Routes</h1>
            <a href="{{ route('delivery-routes.create') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                + New Route
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
                <p class="text-gray-600 text-sm font-semibold uppercase">Total Routes</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['total_routes'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-600">
                <p class="text-gray-600 text-sm font-semibold uppercase">Planned</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['planned_routes'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-600">
                <p class="text-gray-600 text-sm font-semibold uppercase">In Progress</p>
                <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['in_progress_routes'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-600">
                <p class="text-gray-600 text-sm font-semibold uppercase">Completed</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['completed_routes'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-600">
                <p class="text-gray-600 text-sm font-semibold uppercase">Pending Stops</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['total_pending_stops'] }}</p>
            </div>
        </div>

        <!-- Routes Table -->
        <div class="bg-white overflow-hidden shadow-md rounded-lg">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Route #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Driver</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Assistant</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Stops</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($routes as $route)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $route->route_name }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $route->route_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $route->driver?->name ?? 'Not assigned' }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $route->assistant?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-900">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                    {{ $route->stats['total_stops'] ?? 0 }} stops
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $route->stats['completion_percentage'] ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ $route->stats['completion_percentage'] ?? 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $route->route_status === 'Planned' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $route->route_status === 'InProgress' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $route->route_status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $route->route_status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $route->route_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('delivery-routes.show', $route) }}" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 italic">No delivery routes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $routes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
