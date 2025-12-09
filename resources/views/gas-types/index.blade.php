@extends('layouts.app')

@section('content')
<div x-data="{ 
        openModal: false, 
        gas: { id: null, name: '', price: '' } 
    }" class="space-y-8 pb-8">

    <!-- Header -->
    <div class="pt-2 flex flex-col md:flex-row justify-between items-start gap-6">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Gas Types</h1>
        </div>
        <button
            @click="gas = { id: null, name: '', price: '' }; openModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-md flex items-center gap-2 font-semibold">
            + Add Gas Type
        </button>
    </div>
    <hr class="border-gray-300">

    <!-- Success Message -->
    @if(session('success'))
        <div 
        x-data="{ show: true }" x-show="show"
        x-init="setTimeout(() => show = false, 5000)"
        class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md">
            <p class="text-emerald-800 font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($gasTypes as $g)
        <div class="bg-white border rounded-xl shadow-sm p-6 relative"
             x-data="{ gasData: {id: {{ $g->id }}, name: '{{ $g->name }}', price: '{{ $g->price }}'} }">

            <h3 class="text-xl font-bold text-gray-900">{{ $g->name }}</h3>
            <p class="text-gray-600 mt-2">Price: <span class="font-semibold">Rs. {{ $g->price }}</span></p>

            <div class="flex gap-2 mt-5">
                <button 
                    @click="gas = gasData; openModal = true"
                    class="flex-1 bg-amber-50 hover:bg-amber-100 text-amber-700 py-2 rounded-lg">
                    Edit
                </button>

                <form method="POST" action="{{ route('gas-types.destroy',$g->id) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Delete this gas type?')"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-700 py-2 rounded-lg">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $gasTypes->links() }}
    </div>

    <!-- MODAL -->
    <div x-show="openModal" x-cloak class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 z-50">
        <div @click.away="openModal=false" class="bg-white w-full max-w-xl rounded-2xl p-8 shadow-2xl">

            <h2 class="text-3xl font-bold mb-2" 
                x-text="gas.id ? 'Edit Gas Type' : 'Add Gas Type'"></h2>

            <form :action="gas.id ? '/gas-types/' + gas.id : '/gas-types'" method="POST">
                @csrf
                <template x-if="gas.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-5 mt-6">

                    <!-- Name -->
                    <x-form.input 
                        name="name"
                        label="Gas Type Name"
                        placeholder="e.g. 12.5kg"
                        required
                        x-model="gas.name"
                        :error="$errors->first('name')" />

                    <!-- Price -->
                    <x-form.input 
                        name="price"
                        type="number"
                        step="0.01"
                        label="Price (Rs.)"
                        placeholder="Enter price"
                        required
                        x-model="gas.price"
                        :error="$errors->first('price')" />
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                    <x-form.button type="button" variant="outline" @click="openModal=false">
                        Cancel
                    </x-form.button>

                    <x-form.button type="submit" variant="primary">
                        Save Gas Type
                    </x-form.button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
