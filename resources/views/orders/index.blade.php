@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-4xl font-bold text-gray-900">Order Management</h1>
            <a href="{{ route('orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                + Create Order
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <p class="text-xs text-gray-600 uppercase font-semibold">Total Orders</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <p class="text-xs text-gray-600 uppercase font-semibold">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <p class="text-xs text-gray-600 uppercase font-semibold">Loaded</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['loaded'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
                <p class="text-xs text-gray-600 uppercase font-semibold">Delivered</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['delivered'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <p class="text-xs text-gray-600 uppercase font-semibold">Completed</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-600 uppercase font-semibold">Urgent</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['urgent'] }}</p>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white overflow-hidden shadow-md rounded-lg">
            @if($orders->count() > 0)
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Route</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Urgent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $order->customer->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $order->items->count() }} items</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    LKR {{ number_format($order->order_total ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $order->deliveryRoute?->route_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $order->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'Loaded' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $order->status === 'Delivered' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $order->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->is_urgent)
                                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">⚠️ URGENT</span>
                                    @else
                                        <span class="text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    @if($order->canTransitionTo('Loaded'))
                                        <form action="{{ route('orders.status', [$order, 'Loaded']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-800">Load</button>
                                        </form>
                                    @endif
                                    @if($order->canTransitionTo('Delivered'))
                                        <form action="{{ route('orders.status', [$order, 'Delivered']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-800">Deliver</button>
                                        </form>
                                    @endif
                                    @if($order->canTransitionTo('Completed'))
                                        <form action="{{ route('orders.status', [$order, 'Completed']) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800">Complete</button>
                                        </form>
                                    @endif
                                    @if($order->canBeDeleted())
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">No orders found. Create your first order.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
