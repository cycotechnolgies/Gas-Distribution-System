@extends('layouts.app')

@section('content')
<h1 class="text-4xl font-bold mb-6">Stock</h1>

<table class="w-full bg-white shadow rounded-xl text-xs md:text-base">
    <thead class="">
        <tr>
            <th class="p-2 md:p-4 text-left">Gas Type</th>
            <th class="p-2 md:p-4 text-left">Full Cylinders</th>
            <th class="p-2 md:p-4 text-left">Empty Cylinders</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stocks as $s)
        <tr class="border-t">
            <td class="p-2 md:p-4">{{ $s->gasType->name }}</td>
            <td class="p-2 md:p-4 font-bold">{{ $s->full_qty }}</td>
            <td class="p-2 md:p-4">{{ $s->empty_qty }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
