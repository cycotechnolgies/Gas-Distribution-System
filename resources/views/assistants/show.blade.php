@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Assistant Details</h1>
        <a href="{{ route('assistants.index') }}" class="text-gray-600 hover:text-gray-800">← Back to List</a>
    </div>

    <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-200">

        <div class="bg-gradient-to-r from-purple-500 via-purple-500 to-purple-600 px-8 py-10 text-white relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>

            <div class="relative">
                <div class="w-16 h-16 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm mb-4">
                    <span class="text-white text-2xl font-bold">{{ strtoupper(substr($assistant->name, 0, 2)) }}</span>
                </div>

                <h2 class="text-3xl font-semibold">{{ $assistant->name }}</h2>
                <p class="text-purple-100 mt-1">Assistant ID #{{ $assistant->id }}</p>
            </div>
        </div>

        <div class="px-8 py-8 space-y-6">

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Contact Information</h3>
                <p class="text-gray-700"><strong>Phone:</strong> {{ $assistant->phone ?? '—' }}</p>
                <p class="text-gray-700"><strong>Address:</strong> {{ $assistant->address ?? '—' }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Status</h3>
                <span class="px-3 py-1 text-sm rounded-full 
                    {{ $assistant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ ucfirst($assistant->status) }}
                </span>
            </div>

            @if($assistant->notes)
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Notes</h3>
                <p class="text-gray-700">{{ $assistant->notes }}</p>
            </div>
            @endif

        </div>

        <div class="px-8 py-6 bg-gray-50 border-t flex justify-end gap-3">
            <a href="{{ route('assistants.edit', $assistant) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-lg shadow">
                Edit
            </a>

            <form action="{{ route('assistants.destroy', $assistant) }}" method="POST"
                  onsubmit="return confirm('Delete this assistant? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">
                    Delete
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
