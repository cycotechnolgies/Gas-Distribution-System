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
        <div class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 relative group"
             x-data="{ gasData: {id: {{ $g->id }}, name: '{{ $g->name }}', price: '{{ $g->price }}'} }">

            <!-- Header Section -->
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $g->name }}</h3>
                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">Gas Type</span>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 pt-4 border-t border-gray-200">
                <button 
                    @click="window.location.href = '/gas-types/{{ $g->id }}'"
                    class="flex-1 flex justify-center bg-blue-50 hover:bg-blue-500 text-blue-700 hover:text-white py-2.5 rounded-lg font-medium transition-all duration-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                </button>

                <button 
                    @click="gas = gasData; openModal = true"
                    class="flex-1 flex justify-center bg-amber-50 hover:bg-amber-500 text-amber-700 hover:text-white py-2.5 rounded-lg font-medium transition-all duration-200 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen-icon lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                </button>

                <form method="POST" action="{{ route('gas-types.destroy',$g->id) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Delete this gas type?')"
                            class="w-full flex justify-center bg-red-50 hover:bg-red-500 text-red-700 hover:text-white py-2.5 rounded-lg font-medium transition-all duration-200 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
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
