@extends('layouts.app')

@section('content')
<div x-data="refillForm()" class="space-y-8">

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold">Gas Refill Tracking</h1>
            <p class="text-gray-600 mt-2">Track refills performed by suppliers</p>
        </div>
        <button @click="openModal=true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Record Refill
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Refills Table -->
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-4 font-semibold">Refill Ref</th>
                    <th class="p-4 font-semibold">Supplier</th>
                    <th class="p-4 font-semibold">Gas Type</th>
                    <th class="p-4 font-semibold">Cylinders</th>
                    <th class="p-4 font-semibold">Cost/Unit</th>
                    <th class="p-4 font-semibold">Total Cost</th>
                    <th class="p-4 font-semibold">Date</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($refills as $refill)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-semibold text-blue-600">{{ $refill->refill_ref }}</td>
                    <td class="p-4">
                        <a href="{{ route('suppliers.dashboard', $refill->supplier->id) }}" class="text-blue-600 hover:underline">
                            {{ $refill->supplier->name }}
                        </a>
                    </td>
                    <td class="p-4">{{ $refill->gasType->name }}</td>
                    <td class="p-4 font-bold">{{ $refill->cylinders_refilled }}</td>
                    <td class="p-4">Rs. {{ number_format($refill->cost_per_cylinder, 2) }}</td>
                    <td class="p-4 font-bold text-green-600">Rs. {{ number_format($refill->total_cost, 2) }}</td>
                    <td class="p-4">{{ $refill->refill_date->format('d M Y') }}</td>
                    <td class="p-4 flex gap-2">
                        <form method="POST" action="{{ route('refills.destroy', $refill->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this refill record?')" class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs hover:bg-red-200">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-6 text-center text-gray-500">
                        No refills recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $refills->links() }}
    </div>

    <!-- Create Refill Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-2xl p-8 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto">

            <h2 class="text-2xl font-bold mb-6">Record Gas Refill</h2>

            <form method="POST" action="{{ route('refills.store') }}">
                @csrf

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                        <select name="supplier_id" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gas Type *</label>
                        <select name="gas_type_id" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Gas Type</option>
                            @foreach($gasTypes as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Quantities -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cylinders Refilled *</label>
                        <input type="number" name="cylinders_refilled" min="1" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost Per Cylinder *</label>
                        <input type="number" step="0.01" name="cost_per_cylinder" min="0" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Refill Date *</label>
                        <input type="date" name="refill_date" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Add any relevant notes..."></textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button type="button" @click="openModal=false"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        Record Refill
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
function refillForm() {
    return {
        openModal: false
    }
}
</script>
@endsection
