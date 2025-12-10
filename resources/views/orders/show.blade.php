@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
            <div class="mt-2 text-sm text-gray-600">
                <span>Customer: <strong>{{ $order->customer->name }}</strong></span>
                <span class="mx-2">•</span>
                <span>Date: {{ $order->order_date->format('Y-m-d') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($order->urgent)
                <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-semibold">URGENT</span>
            @endif

            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $order->status == 'Delivered' ? 'bg-green-100 text-green-700' : '' }}
                {{ $order->status == 'Loaded' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $order->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $order->status == 'Completed' ? 'bg-gray-100 text-gray-700' : '' }}">
                {{ $order->status }}
            </span>

            <a href="{{ route('orders.index') }}" class="inline-block ml-2 px-3 py-2 bg-gray-50 border rounded text-sm">Back</a>
        </div>
    </div>

    <!-- Order Summary Card -->
    <div class="bg-white rounded-xl shadow p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1">
            <p class="text-gray-600">Customer</p>
            <h2 class="text-xl font-semibold">{{ $order->customer->name }}</h2>
            @if($order->customer->phone)
                <p class="text-sm text-gray-500 mt-1">Phone: {{ $order->customer->phone }}</p>
            @endif
            @if($order->customer->address)
                <p class="text-sm text-gray-500 mt-1">Address: {{ $order->customer->address }}</p>
            @endif
        </div>

        <div class="text-right">
            <p class="text-sm text-gray-500">Order Total</p>
            <div class="text-2xl font-bold">Rs. {{ number_format($order->total_amount,2) }}</div>
            <p class="text-sm text-gray-500 mt-1">Route: {{ $order->deliveryRoute?->route_name ?? '-' }}</p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Gas Type</th>
                    <th class="p-4">Quantity</th>
                    <th class="p-4">Unit Price</th>
                    <th class="p-4">Line Total</th>
                    <th class="p-4">Stock Available</th>
                    <th class="p-4">Adjusted</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                @php
                    // get current stock for display (may be null)
                    $stock = \App\Models\Stock::where('gas_type_id', $item->gas_type_id)->first();
                    $available = $stock ? (int) $stock->full_qty : 0;
                @endphp
                <tr class="border-t">
                    <td class="p-4">
                        <div class="font-semibold">{{ $item->gasType->name }}</div>
                    </td>
                    <td class="p-4">{{ $item->quantity }}</td>
                    <td class="p-4">Rs. {{ number_format($item->unit_price,2) }}</td>
                    <td class="p-4">Rs. {{ number_format($item->total,2) }}</td>
                    <td class="p-4">
                        <span class="text-sm {{ $available >= $item->quantity ? 'text-green-700' : 'text-red-600' }}">
                            {{ $available }}
                        </span>
                    </td>
                    <td class="p-4">
                        @if($item->delivered_adjusted)
                            <span class="text-sm text-gray-600">Yes</span>
                        @else
                            <span class="text-sm text-gray-400">No</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Actions -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex flex-wrap gap-3">
            <!-- Mark Loaded -->
            <form method="POST" action="{{ route('orders.status', [$order->id, 'Loaded']) }}">
                @csrf
                <button type="submit"
                    @if($order->status == 'Loaded' || $order->status == 'Delivered' || $order->status == 'Completed') disabled @endif
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50">
                    Mark Loaded
                </button>
            </form>

            <!-- Mark Delivered -->
            <form method="POST" action="{{ route('orders.status', [$order->id, 'Delivered']) }}">
                @csrf
                <button type="submit"
                    @if($order->status == 'Delivered' || $order->status == 'Completed') disabled @endif
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                    onclick="return confirm('Mark this order as Delivered? This will adjust stock quantities if available.')">
                    Mark Delivered
                </button>
            </form>

            <!-- Mark Completed -->
            <form method="POST" action="{{ route('orders.status', [$order->id, 'Completed']) }}">
                @csrf
                <button type="submit"
                    @if($order->status == 'Completed') disabled @endif
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 disabled:opacity-50">
                    Complete
                </button>
            </form>
        </div>

        <div class="text-sm text-gray-600">
            <p>Last updated: {{ $order->updated_at->diffForHumans() }}</p>
            <p class="mt-1">Created: {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <!-- Delivery Audit / Notes (optional) -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-2">Delivery Notes / Audit</h3>
        <p class="text-sm text-gray-600">
            <!-- Placeholder - create audit table later -->
            No delivery notes yet. When deliveries are processed, create audit entries showing who delivered and adjustments.
        </p>
    </div>

</div>
@endsection
