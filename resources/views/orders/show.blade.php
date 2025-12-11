@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Orders</a>
            </div>
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">{{ $order->order_number }}</h1>
                    <p class="text-gray-600 mt-2">Customer: <span class="font-semibold">{{ $order->customer->name }}</span></p>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 rounded-full text-sm font-bold
                        {{ $order->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->status === 'Loaded' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $order->status === 'Delivered' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $order->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $order->status }}
                    </span>
                    @if($order->is_urgent)
                        <span class="ml-2 px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800">⚠️ URGENT</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Left: Order Details -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Order Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Order Date</p>
                            <p class="text-lg text-gray-900">{{ $order->order_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Delivery Route</p>
                            <p class="text-lg text-gray-900">{{ $order->deliveryRoute?->route_name ?? 'Not assigned' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Created</p>
                            <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase font-semibold">Last Updated</p>
                            <p class="text-sm text-gray-600">{{ $order->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-600 uppercase font-semibold">Notes</p>
                            <p class="text-gray-900 mt-2">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Order Items -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Cylinder Order Details</h2>

                    @if($order->items->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">Gas Type</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-900">Qty</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-900">Unit Price</th>
                                        <th class="px-4 py-2 text-right font-semibold text-gray-900">Line Total</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-900">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($order->items as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->gasType->name }}</td>
                                            <td class="px-4 py-3 text-right text-gray-900">{{ $item->quantity }}</td>
                                            <td class="px-4 py-3 text-right text-gray-900">LKR {{ number_format($item->unit_price, 2) }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-gray-900">LKR {{ number_format($item->line_total, 2) }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-600">{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No items in this order.</p>
                    @endif
                </div>
            </div>

            <!-- Right: Summary & Actions -->
            <div>
                <!-- Order Summary -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Items:</span>
                            <span class="font-semibold text-gray-900">{{ $stats['item_count'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Cylinders:</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_qty'] }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Order Total:</span>
                            <span class="text-2xl font-bold text-blue-600">LKR {{ number_format($stats['order_total'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Customer</h2>

                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-gray-600">Name</p>
                            <p class="font-semibold text-gray-900">{{ $order->customer->name }}</p>
                        </div>
                        @if($order->customer->phone)
                            <div>
                                <p class="text-gray-600">Phone</p>
                                <p class="text-gray-900">{{ $order->customer->phone }}</p>
                            </div>
                        @endif
                        @if($order->customer->address)
                            <div>
                                <p class="text-gray-600">Address</p>
                                <p class="text-gray-900">{{ $order->customer->address }}</p>
                            </div>
                        @endif
                        <div class="pt-2 border-t border-gray-200">
                            <p class="text-xs text-gray-600 uppercase font-semibold">Type</p>
                            <span class="inline-block px-2 py-1 rounded text-xs font-medium
                                {{ $order->customer->customer_type === 'Dealer' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->customer->customer_type === 'Commercial' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $order->customer->customer_type === 'Individual' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ $order->customer->customer_type }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Status Transitions -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Status Transitions</h2>

                    <div class="space-y-2">
                        @if($order->canTransitionTo('Loaded'))
                            <form action="{{ route('orders.status', [$order, 'Loaded']) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">
                                    Mark as Loaded
                                </button>
                            </form>
                        @endif

                        @if($order->canTransitionTo('Delivered'))
                            <form action="{{ route('orders.status', [$order, 'Delivered']) }}" method="POST" onsubmit="return confirm('Mark order as delivered? Stock will be adjusted.');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-medium">
                                    Mark as Delivered
                                </button>
                            </form>
                        @endif

                        @if($order->canTransitionTo('Completed'))
                            <form action="{{ route('orders.status', [$order, 'Completed']) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                    Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if($order->canTransitionTo('Cancelled'))
                            <form action="{{ route('orders.status', [$order, 'Cancelled']) }}" method="POST" onsubmit="return confirm('Cancel this order?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                                    Cancel Order
                                </button>
                            </form>
                        @endif

                        @if(!$stats['can_transition'] && $order->status !== 'Completed')
                            <p class="text-xs text-gray-600 italic">No further transitions available for this order status.</p>
                        @endif
                    </div>

                    @if($order->canBeDeleted())
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this order? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm font-medium">
                                    Delete Order
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Order Timeline</h2>

            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 bg-blue-600 rounded-full"></div>
                        <div class="w-0.5 h-12 bg-gray-200"></div>
                    </div>
                    <div class="pb-4">
                        <p class="font-semibold text-gray-900">Order Created</p>
                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                @if($order->loaded_at)
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 bg-purple-600 rounded-full"></div>
                            <div class="w-0.5 h-12 bg-gray-200"></div>
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold text-gray-900">Marked as Loaded</p>
                            <p class="text-sm text-gray-600">{{ $order->loaded_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->delivered_at)
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 bg-orange-600 rounded-full"></div>
                            <div class="w-0.5 h-12 bg-gray-200"></div>
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold text-gray-900">Marked as Delivered</p>
                            <p class="text-sm text-gray-600">{{ $order->delivered_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->completed_at)
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 bg-green-600 rounded-full"></div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Order Completed</p>
                            <p class="text-sm text-gray-600">{{ $order->completed_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
