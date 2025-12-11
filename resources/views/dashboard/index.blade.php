@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 gap-3 md:grid-cols-5 md:gap-6 mb-8">
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">
        <h3 class="text-gray-500 text-xs md:text-base">Suppliers</h3>
        <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $stats['suppliers'] }}</h1>
    </div>
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">
        <h3 class="text-gray-500 text-xs md:text-base">Customers</h3>
        <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $stats['customers'] }}</h1>
    </div>
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">
        <h3 class="text-gray-500 text-xs md:text-base">Pending Orders</h3>
        <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $stats['pending_orders'] }}</h1>
    </div>
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">
        <h3 class="text-gray-500 text-xs md:text-base">Stock Items</h3>
        <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $stats['stock_items'] }}</h1>
    </div>
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">
        <h3 class="text-gray-500 text-xs md:text-base">Routes Today</h3>
        <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $stats['routes_today'] }}</h1>
    </div>
</div>

<div class="grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-6 mt-8">
    <!-- Orders Line Chart by Status -->
    <div class="bg-white p-4 md:p-6 rounded shadow">
        <h2 class="text-base md:text-lg font-semibold mb-4">Orders per Month by Status</h2>
        <canvas id="ordersLineChart" height="120"></canvas>
    </div>
    <!-- Stock Bar Chart by Gas Type -->
    <div class="bg-white p-4 md:p-6 rounded shadow">
        <h2 class="text-base md:text-lg font-semibold mb-4">Stock by Gas Type</h2>
        <canvas id="stockBarChart" height="120"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Orders Line Chart by Status
    const ordersPerMonthByStatus = @json($ordersPerMonthByStatus);
    const statuses = @json($statuses);
    // Get all months present in any status
    let allMonths = new Set();
    Object.values(ordersPerMonthByStatus).forEach(obj => {
        Object.keys(obj).forEach(m => allMonths.add(m));
    });
    allMonths = Array.from(allMonths).map(Number).sort((a, b) => a - b);
    // Month labels
    const monthLabels = allMonths.map(m => new Date(0, m - 1).toLocaleString('default', { month: 'short' }));
    // Datasets for each status
    const statusColors = [
        'rgba(54, 162, 235, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ];
    const datasets = statuses.map((status, idx) => {
        const data = allMonths.map(m => ordersPerMonthByStatus[status][m] || 0);
        return {
            label: status,
            data: data,
            borderColor: statusColors[idx % statusColors.length],
            backgroundColor: statusColors[idx % statusColors.length],
            fill: false,
            tension: 0.2
        };
    });
    new Chart(document.getElementById('ordersLineChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Stock Bar Chart by Gas Type
    const stockByGasType = @json($stockByGasType);
    new Chart(document.getElementById('stockBarChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: Object.keys(stockByGasType),
            datasets: [{
                label: 'Stock',
                data: Object.values(stockByGasType),
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
@endsection
