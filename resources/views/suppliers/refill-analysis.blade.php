@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('suppliers.dashboard', $supplier->id) }}" class="text-blue-600 hover:underline mb-2 inline-block">← Back to Dashboard</a>
            <h1 class="text-4xl font-bold">{{ $supplier->name }} - Refill Analysis</h1>
            <p class="text-gray-600 mt-2">Track gas refill activity and costs</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total Refills</p>
            <h3 class="text-3xl font-bold mt-2">{{ $totalRefills }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Cylinders Refilled</p>
            <h3 class="text-3xl font-bold mt-2">{{ $totalCylinders }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total Refill Cost</p>
            <h3 class="text-3xl font-bold mt-2 text-green-600">Rs. {{ number_format($totalCost, 2) }}</h3>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Avg Cost per Cylinder</p>
            <h3 class="text-3xl font-bold mt-2 text-blue-600">Rs. {{ number_format($averageCostPerCylinder, 2) }}</h3>
        </div>
    </div>

    <!-- Refills by Gas Type -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Refills by Gas Type</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-4 font-semibold">Gas Type</th>
                        <th class="p-4 font-semibold text-right">Cylinders Refilled</th>
                        <th class="p-4 font-semibold text-right">Avg Cost/Unit</th>
                        <th class="p-4 font-semibold text-right">Total Cost</th>
                        <th class="p-4 font-semibold text-center">Refill Count</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($refillsByType as $refill)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-medium">{{ $refill['gas_type'] }}</td>
                        <td class="p-4 text-right font-bold">{{ $refill['total_cylinders'] }}</td>
                        <td class="p-4 text-right">Rs. {{ number_format($refill['average_cost'], 2) }}</td>
                        <td class="p-4 text-right font-bold text-green-600">Rs. {{ number_format($refill['total_cost'], 2) }}</td>
                        <td class="p-4 text-center">{{ $refill['refill_count'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No refill data available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Refill Trend -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Refill Trend</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-4 font-semibold">Month</th>
                        <th class="p-4 font-semibold text-right">Cylinders</th>
                        <th class="p-4 font-semibold text-right">Total Cost</th>
                        <th class="p-4 font-semibold text-right">Avg Cost/Unit</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($monthlyRefills as $monthly)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-medium">{{ $monthly['month'] }}</td>
                        <td class="p-4 text-right font-bold">{{ $monthly['cylinders'] }}</td>
                        <td class="p-4 text-right font-bold">Rs. {{ number_format($monthly['cost'], 2) }}</td>
                        <td class="p-4 text-right">Rs. {{ number_format($monthly['average_cost'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500">
                            No monthly data available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Refills -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Refills</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-4 font-semibold">Refill Ref</th>
                        <th class="p-4 font-semibold">Gas Type</th>
                        <th class="p-4 font-semibold text-right">Cylinders</th>
                        <th class="p-4 font-semibold text-right">Cost/Unit</th>
                        <th class="p-4 font-semibold text-right">Total Cost</th>
                        <th class="p-4 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($refills as $refill)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-semibold text-blue-600">{{ $refill->refill_ref }}</td>
                        <td class="p-4">{{ $refill->gasType->name }}</td>
                        <td class="p-4 text-right font-bold">{{ $refill->cylinders_refilled }}</td>
                        <td class="p-4 text-right">Rs. {{ number_format($refill->cost_per_cylinder, 2) }}</td>
                        <td class="p-4 text-right font-bold">Rs. {{ number_format($refill->total_cost, 2) }}</td>
                        <td class="p-4">{{ $refill->refill_date->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">
                            No refills recorded
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
