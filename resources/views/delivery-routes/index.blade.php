@extends('layouts.app')

@section('content')
<div x-data="deliveryRoutes()" class="space-y-8 pb-8">

    <!-- Page Header -->
    <div class="pt-2">
        <div class="flex flex-col gap-8 md:flex-row justify-between items-start">
            <h1 class="text-4xl font-bold text-gray-900">Delivery Routes</h1>

            <button @click="resetForm(); openModal = true"
                class="w-full md:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all">
                Add Route
            </button>
        </div>
    </div>
    <hr class="border-gray-400">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-600 text-emerald-700 p-4 rounded shadow">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-600 text-red-700 p-4 rounded shadow">
            {{ session('error') }}
        </div>
    @endif

    <!-- Routes Grid -->
    @if($routes->count())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($routes as $route)
                <div class="bg-white rounded-xl border shadow-sm hover:shadow-xl transition overflow-hidden"
                     x-data="{ routeItem: @json($route) }">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-5 text-white relative">
                        <h2 class="text-xl font-bold">{{ $route->name }}</h2>
                        <p class="text-white/80 text-sm mt-1">
                            {{ $route->description ?? 'No description provided.' }}
                        </p>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-4 bg-gray-50 border-t space-y-2">
                        <p><strong>Driver:</strong>
                            @if($route->driver)
                                <span class="text-indigo-700">{{ $route->driver->name }}</span>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </p>

                        <p><strong>Assistant:</strong>
                            @if($route->assistant)
                                <span class="text-purple-700">{{ $route->assistant->name }}</span>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </p>

                        <p><strong>Vehicle:</strong>
                            @if($route->vehicle)
                                <span class="text-sky-700">{{ $route->vehicle->vehicle_number }}</span>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 flex gap-2 bg-white border-t">

                        <a href="{{ route('routes.show', $route) }}"
                           class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-2 rounded text-center">
                            View
                        </a>

                        <button @click="edit(routeItem)" class="flex-1 bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-2 rounded">
                            Edit
                        </button>

                        <form action="{{ route('routes.destroy', $route) }}" method="POST"
                              class="flex-1"
                              onsubmit="return confirm('Delete this route?')">
                            @csrf @method('DELETE')
                            <button class="w-full bg-red-50 hover:bg-red-100 text-red-700 px-3 py-2 rounded">
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $routes->links() }}</div>

    @else
        <div class="bg-white border rounded-xl p-16 text-center shadow">
            <h2 class="text-2xl font-bold text-gray-800 mb-3">No Routes Yet</h2>
            <p class="text-gray-600 mb-6">Create a delivery route to start assigning drivers, assistants, and vehicles.</p>

            <button @click="resetForm(); openModal = true"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:shadow-lg">
                Add First Route
            </button>
        </div>
    @endif

    <!-- Modal -->
    <div x-show="openModal"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         style="display: none;">

        <div @click.away="openModal = false"
             class="bg-white p-8 rounded-xl w-full max-w-2xl shadow-xl max-h-[90vh] overflow-y-auto">

            <h2 class="text-3xl font-bold text-gray-900 mb-2"
                x-text="form.id ? 'Edit Route' : 'Add Delivery Route'"></h2>
            <p class="text-gray-600 mb-6">Assign driver, assistant, and vehicle to this route.</p>

            <form method="POST" :action="submitUrl()">
                @csrf
                <template x-if="form.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <x-form.error-summary />

                <div class="space-y-5">

                    <x-form.input name="name" label="Route Name" required x-model="form.name" />

                    <x-form.textarea name="description"
                                     label="Description"
                                     placeholder="Optional description"
                                     x-model="form.description"></x-form.textarea>

                    <!-- Driver -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Driver</label>
                        <select name="driver_id" x-model="form.driver_id"
                                class="mt-1 w-full border-gray-300 rounded">
                            <option value="">— Select Driver —</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assistant -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Assistant</label>
                        <select name="assistant_id" x-model="form.assistant_id"
                                class="mt-1 w-full border-gray-300 rounded">
                            <option value="">— Select Assistant —</option>
                            @foreach($assistants as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Vehicle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Vehicle</label>
                        <select name="vehicle_id" x-model="form.vehicle_id"
                                class="mt-1 w-full border-gray-300 rounded">
                            <option value="">— Select Vehicle —</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->vehicle_number }} ({{ $v->type }})</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                    <button type="button" @click="openModal=false"
                            class="px-5 py-2 rounded border bg-white hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                        Save Route
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>


<script>
function deliveryRoutes() {
    return {
        openModal: false,
        form: {
            id: null,
            name: '',
            description: '',
            driver_id: '',
            assistant_id: '',
            vehicle_id: '',
        },

        submitUrl() {
            return this.form.id
                ? `/routes/${this.form.id}`
                : '/routes';
        },

        resetForm() {
            this.form = {
                id: null,
                name: '',
                description: '',
                driver_id: '',
                assistant_id: '',
                vehicle_id: '',
            };
        },

        edit(route) {
            this.form = JSON.parse(JSON.stringify(route));
            this.openModal = true;
        }
    };
}
</script>

@endsection
