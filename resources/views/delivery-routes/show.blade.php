@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Route Details</h1>
        <a href="{{ route('routes.index') }}" class="text-gray-600 hover:text-gray-800">← Back</a>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">

        <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-10 text-white relative">
            <h2 class="text-3xl font-semibold">{{ $route->name }}</h2>
            <p class="text-white/80 mt-2">{{ $route->description }}</p>
        </div>

        <div class="px-8 py-8 space-y-8">

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Assigned Driver</h3>
                <p class="text-gray-700">
                    @if($route->driver)
                        <a href="{{ route('drivers.show', $route->driver) }}" class="text-indigo-600 hover:underline">
                            {{ $route->driver->name }}
                        </a>
                    @else
                        <span class="text-gray-500">Not assigned</span>
                    @endif
                </p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Assistant</h3>
                <p class="text-gray-700">
                    @if($route->assistant)
                        <a href="{{ route('assistants.show', $route->assistant) }}" class="text-purple-600 hover:underline">
                            {{ $route->assistant->name }}
                        </a>
                    @else
                        <span class="text-gray-500">Not assigned</span>
                    @endif
                </p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Vehicle</h3>
                <p class="text-gray-700">
                    @if($route->vehicle)
                        <a href="{{ route('vehicles.show', $route->vehicle) }}" class="text-sky-600 hover:underline">
                            {{ $route->vehicle->vehicle_number }}
                        </a>
                    @else
                        <span class="text-gray-500">Not assigned</span>
                    @endif
                </p>
            </div>

        </div>

        <div class="px-8 py-6 bg-gray-50 border-t flex justify-end gap-3">
            <a href="{{ route('routes.edit', $route) }}"
                class="px-5 py-2 rounded bg-amber-500 text-white hover:bg-amber-600">
                Edit
            </a>

            <form method="POST" action="{{ route('routes.destroy', $route) }}"
                  onsubmit="return confirm('Delete this route? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="px-5 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
