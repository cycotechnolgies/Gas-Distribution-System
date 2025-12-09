@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
])

@php
    $baseClasses = 'font-semibold transition-all duration-200 flex items-center justify-center gap-2 rounded-lg';
    
    $sizeClasses = match($size) {
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-2.5 text-base',
        'lg' => 'px-8 py-3 text-lg',
        default => 'px-6 py-2.5 text-base',
    };

    $variantClasses = match($variant) {
        'primary' => 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white shadow-md hover:shadow-lg disabled:from-gray-400 disabled:to-gray-400',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800 disabled:bg-gray-100',
        'danger' => 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow-md hover:shadow-lg disabled:from-gray-400 disabled:to-gray-400',
        'success' => 'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-md hover:shadow-lg disabled:from-gray-400 disabled:to-gray-400',
        'outline' => 'border-2 border-gray-300 text-gray-700 hover:bg-gray-50 disabled:border-gray-200 disabled:text-gray-400',
        default => 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white',
    };
@endphp

<button
    type="{{ $type }}"
    @if($disabled || $loading) disabled @endif
    {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}" . ($disabled || $loading ? ' opacity-60 cursor-not-allowed' : '')]) }}
>
    @if($loading)
        <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    
    {{ $slot }}
</button>
