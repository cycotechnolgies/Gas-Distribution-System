@php
    $isEdit = isset($driver);
@endphp

<form action="{{ $isEdit ? route('drivers.update', $driver) : route('drivers.store') }}" method="POST" class="space-y-4">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div>
        <label class="block text-sm font-medium">Name</label>
        <input name="name" value="{{ old('name', $driver->name ?? '') }}" class="mt-1 block w-full rounded border-gray-300" required>
        @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Phone</label>
        <input name="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="mt-1 block w-full rounded border-gray-300">
        @error('phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">License Number</label>
        <input name="license_number" value="{{ old('license_number', $driver->license_number ?? '') }}" class="mt-1 block w-full rounded border-gray-300">
        @error('license_number') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Status</label>
        <select name="status" class="mt-1 block w-full rounded border-gray-300">
            <option value="active" {{ old('status', $driver->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $driver->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Notes</label>
        <textarea name="notes" class="mt-1 block w-full rounded border-gray-300">{{ old('notes', $driver->notes ?? '') }}</textarea>
    </div>

    <div class="pt-4">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
            {{ $isEdit ? 'Update' : 'Create' }}
        </button>
        <a href="{{ route('drivers.index') }}" class="ml-2 text-gray-600">Cancel</a>
    </div>
</form>
