@extends('layouts.app')

@section('content')
<div
    x-data="{
        openModal: false,
        form: { supplier_id: '', rate: '' }
    }"
    class="space-y-8 pb-8"
>

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">
                {{ $gasType->name }}
            </h1>
            <p class="text-gray-600 mt-1">Supplier Rates</p>
        </div>

        <button
            @click="form = { supplier_id: '', rate: '' }; openModal = true"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:shadow-lg"
        >
            + Add Supplier Rate
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="p-4">Supplier</th>
                    <th class="p-4">Rate (Rs.)</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gasType->suppliers as $s)
                    <tr class="border-t">
                        <td class="p-4 font-semibold">{{ $s->name }}</td>
                        <td class="p-4">Rs. {{ $s->pivot->rate }}</td>
                        <td class="p-4 text-right flex justify-end gap-2">

                            <!-- Edit -->
                            <button
                                @click="
                                    form = {
                                        supplier_id: '{{ $s->id }}',
                                        rate: '{{ $s->pivot->rate }}'
                                    };
                                    openModal = true
                                "
                                class="bg-amber-100 text-amber-700 px-3 py-1 rounded-lg"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen-icon lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                        </button>

                            <!-- Delete -->
                            <form method="POST"
                                  action="{{ route('gas-types.remove-supplier', [$gasType->id, $s->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    onclick="return confirm('Remove this supplier?')"
                                    class="bg-red-100 text-red-700 px-3 py-1 rounded-lg"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-6 text-center text-gray-500">
                            No suppliers added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div @click.away="openModal=false"
             class="bg-white w-full max-w-xl rounded-xl p-8 shadow-2xl">

            <h2 class="text-2xl font-bold mb-6">
                Add / Update Supplier Rate
            </h2>

            <form method="POST"
                  action="{{ route('gas-types.supplier-rate', $gasType->id) }}">
                @csrf

                <div class="space-y-5">

                    <!-- Supplier -->
                    <select name="supplier_id" x-model="form.supplier_id"
                            class="w-full border p-3 rounded-lg" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>

                    <!-- Rate -->
                    <input type="number" step="0.01"
                           name="rate"
                           x-model="form.rate"
                           placeholder="Enter rate"
                           class="w-full border p-3 rounded-lg"
                           required>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="openModal=false"
                            class="bg-gray-200 px-4 py-2 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
