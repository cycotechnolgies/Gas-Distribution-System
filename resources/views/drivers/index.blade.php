@extends('layouts.app')

@section('content')
<div x-data="{ openModal: false, driver: { id: null, name: '', phone: '', license_number: '', address: '', status: 'active', notes: '' } }" class="space-y-8 pb-8">

    <!-- Page Header -->
    <div class="pt-2">
        <div class="flex flex-col gap-8 md:flex-row justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Drivers</h1>
            </div>
            <button @click="driver = { id: null, name: '', phone: '', license_number: '', address: '', status: 'active', notes: '' }; openModal=true"
                    class="w-full md:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 font-semibold">
                <!-- plus icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Driver
            </button>
        </div>
    </div>
    <hr class="border-t border-gray-400">

    <!-- Success / Error -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="bg-gradient-to-r from-emerald-50 to-teal-50 border-l-4 border-emerald-500 rounded-lg p-4 flex items-center gap-3 shadow-sm transition-opacity duration-500" :class="show ? 'opacity-100' : 'opacity-0'">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div>
                <p class="text-emerald-800 font-semibold">Success</p>
                <p class="text-emerald-700 text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-4 text-red-700">{{ session('error') }}</div>
    @endif

    <!-- Cards Grid -->
    @if($drivers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($drivers as $d)
            <div class="group bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-200 hover:border-blue-300 transition-all duration-300 overflow-hidden"
                 x-data="{ item: @json($d) }">
                <div class="bg-gradient-to-r from-indigo-500 via-indigo-500 to-indigo-600 px-6 py-5 relative">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                    <div class="relative">
                        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center mb-3 backdrop-blur-sm">
                            <span class="text-white text-lg font-bold">{{ strtoupper(substr($d->name, 0, 2)) }}</span>
                        </div>
                        <h3 class="text-white font-bold text-lg line-clamp-2">{{ $d->name }}</h3>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-2">
                    <a href="{{ route('drivers.show', $d->id) }}"
                       class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 hover:text-indigo-700 px-3 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 font-medium text-sm"
                       title="View driver">
                        <!-- eye -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </a>

                    <button @click="driver = item; openModal=true"
                            class="flex-1 bg-amber-50 hover:bg-amber-100 text-amber-600 hover:text-amber-700 px-3 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 font-medium text-sm"
                            title="Edit driver">
                        <!-- edit -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                    </button>

                    <form action="{{ route('drivers.destroy', $d->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 px-3 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 font-medium text-sm"
                                onclick="return confirm('Delete this driver? This action cannot be undone.')">
                            <!-- trash -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            <div class="text-gray-700">
                {{ $drivers->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="text-center py-20 px-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-6">
                    <!-- icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No Drivers Yet</h3>
                <p class="text-gray-600 mb-8 max-w-sm mx-auto">Add your first driver to start assigning deliveries and vehicles.</p>
                <button @click="driver = { id: null, name: '', phone: '', license_number: '', address: '', status: 'active', notes: '' }; openModal=true"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-lg inline-flex items-center gap-2 font-semibold shadow-lg">
                    Add First Driver
                </button>
            </div>
        </div>
    @endif

    <!-- Modal -->
    <div x-show="openModal" @keydown.escape="openModal=false" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="display: none;">
        <div @click.away="openModal=false" class="bg-white rounded-xl w-full max-w-2xl p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="mb-6 sticky top-0 bg-white">
                <h2 class="text-3xl font-bold text-gray-900" x-text="driver.id ? 'Edit Driver' : 'Add New Driver'"></h2>
                <p class="text-gray-600 text-sm mt-2" x-text="driver.id ? 'Update driver information' : 'Create a new driver record'"></p>
            </div>

            <form :action="driver.id ? '/drivers/' + driver.id : '/drivers'" method="POST">
                @csrf
                <template x-if="driver.id">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <x-form.error-summary />

                <div class="space-y-5 mb-6">
                    <x-form.input name="name" label="Name" placeholder="Full name" required x-model="driver.name" :error="$errors->first('name')" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input name="phone" label="Phone" placeholder="Phone number" x-model="driver.phone" :error="$errors->first('phone')" />
                        <x-form.input name="license_number" label="License Number" placeholder="Driver license" x-model="driver.license_number" :error="$errors->first('license_number')" />
                    </div>

                    <x-form.input name="address" label="Address" placeholder="Address (optional)" x-model="driver.address" :error="$errors->first('address')" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" x-model="driver.status" class="mt-1 w-full rounded border-gray-300">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <input name="notes" x-model="driver.notes" class="mt-1 w-full rounded border-gray-300" placeholder="Optional notes" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 sticky bottom-0 bg-white">
                    <x-form.button type="button" variant="outline" size="md" @click="openModal=false">Cancel</x-form.button>
                    <x-form.button type="submit" variant="primary" size="md">
                        <!-- save icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline></svg>
                        Save Driver
                    </x-form.button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
