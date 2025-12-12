@extends('layouts.app')

@section('content')
    <div class="w-full py-4 px-2 sm:px-4">
        <!-- Header Section -->
        <div class="mb-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">Supplier Details</h1>
                </div>
                <a href="{{ route('suppliers.index') }}" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition flex items-center gap-2 text-sm sm:text-base whitespace-nowrap">
                    <i class="fas fa-arrow-left"></i> <span class="hidden sm:inline">Back to Suppliers</span><span class="sm:hidden">Back</span>
                </a>
            </div>
        </div>

        <!-- Supplier Information Card -->
        <div class="mb-4 flex flex-col lg:flex-row gap-4">
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-blue-600 text-white py-3 px-4 sm:px-6">
                        <h5 class="mb-0 text-base sm:text-lg font-semibold flex items-center gap-2 truncate">
                            <i class="fas fa-building flex-shrink-0"></i> <span class="truncate">{{ $supplier->name }}</span>
                        </h5>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div class="mb-0 min-w-0">
                                <label class="text-gray-500 text-xs uppercase font-semibold block">Contact Person</label>
                                <p class="mb-0 font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $supplier->name }}</p>
                            </div>
                            <div class="mb-0 min-w-0">
                                <label class="text-gray-500 text-xs uppercase font-semibold block">Phone</label>
                                <p class="mb-0 font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $supplier->phone }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div class="mb-0 min-w-0">
                                <label class="text-gray-500 text-xs uppercase font-semibold block">Email</label>
                                <p class="mb-0 font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $supplier->email }}</p>
                            </div>
                            <div class="mb-0 min-w-0">
                                <label class="text-gray-500 text-xs uppercase font-semibold block">Address</label>
                                <p class="mb-0 font-semibold text-gray-900 text-sm sm:text-base line-clamp-2">{{ $supplier->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="w-full lg:w-1/3 flex flex-col gap-3">
                <div class="bg-blue-500 text-white rounded-lg shadow-md">
                    <div class="p-4 sm:p-6">
                        <div class="flex justify-between items-center gap-3">
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm mb-0 opacity-90">Total POs</p>
                                <h3 class="mb-0 text-2xl sm:text-3xl font-bold">{{ $purchaseOrders->count() }}</h3>
                            </div>
                            <i class="fas fa-file-invoice text-3xl sm:text-4xl opacity-30 flex-shrink-0"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-green-500 text-white rounded-lg shadow-md">
                    <div class="p-4 sm:p-6">
                        <div class="flex justify-between items-center gap-3">
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm mb-0 opacity-90">Total GRNs</p>
                                <h3 class="mb-0 text-2xl sm:text-3xl font-bold">{{ $grns->count() }}</h3>
                            </div>
                            <i class="fas fa-box text-3xl sm:text-4xl opacity-30 flex-shrink-0"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for Related Data -->
        <div class="w-full overflow-hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden" x-data="{ activeTab: 'po' }">
                <div class="border-b border-gray-200 bg-gray-50 overflow-x-auto">
                    <div class="flex gap-0 min-w-min sm:min-w-full">
                        <button @click="activeTab = 'po'" :class="{ 'border-b-2 border-blue-500 text-blue-600': activeTab === 'po', 'text-gray-600': activeTab !== 'po' }" class="px-3 sm:px-6 py-3 font-medium transition-colors hover:text-blue-600 whitespace-nowrap text-sm sm:text-base">
                            <i class="fas fa-file-alt mr-1 sm:mr-2"></i> <span class="hidden sm:inline">Purchase Orders</span><span class="sm:hidden">POs</span>
                        </button>
                        <button @click="activeTab = 'grn'" :class="{ 'border-b-2 border-blue-500 text-blue-600': activeTab === 'grn', 'text-gray-600': activeTab !== 'grn' }" class="px-3 sm:px-6 py-3 font-medium transition-colors hover:text-blue-600 whitespace-nowrap text-sm sm:text-base">
                            <i class="fas fa-clipboard-list mr-1 sm:mr-2"></i> <span class="hidden sm:inline">GRNs</span><span class="sm:hidden">GRNs</span>
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="overflow-x-auto">
                    <!-- Purchase Orders Tab -->
                    <div x-show="activeTab === 'po'">
                        <table class="w-full border-collapse text-sm sm:text-base">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">PO Number</th>
                                    <th class="hidden sm:table-cell px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Amount</th>
                                    <th class="hidden md:table-cell px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $po)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900">{{ $po->po_number }}</td>
                                        <td class="hidden sm:table-cell px-6 py-4 text-sm text-gray-600">{{ $po->order_date?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-semibold text-gray-900">LKR {{ number_format($po->total_amount, 2) }}</td>
                                        <td class="hidden md:table-cell px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                @if($po->status === 'Completed') bg-green-100 text-green-800
                                                @elseif($po->status === 'Pending') bg-yellow-100 text-yellow-800
                                                @elseif($po->status === 'In Progress') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $po->status }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="text-blue-600 hover:text-blue-900 text-xs sm:text-sm font-medium">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500 py-8">
                                            <i class="fas fa-inbox text-2xl opacity-50 mb-2 block"></i>
                                            <p class="text-sm">No purchase orders found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- GRNs Tab -->
                    <div x-show="activeTab === 'grn'">
                        <table class="w-full border-collapse text-sm sm:text-base">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">GRN Number</th>
                                    <th class="hidden sm:table-cell px-6 py-3 text-left text-sm font-semibold text-gray-700">PO Reference</th>
                                    <th class="hidden md:table-cell px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grns as $grn)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-medium text-gray-900">{{ $grn->grn_number }}</td>
                                        <td class="hidden sm:table-cell px-6 py-4 text-sm text-gray-600">{{ $grn->purchaseOrder?->po_number ?? 'N/A' }}</td>
                                        <td class="hidden md:table-cell px-6 py-4 text-sm text-gray-600">{{ $grn->received_date?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm">
                                            <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold
                                                @if($grn->approved) bg-green-100 text-green-800
                                                @elseif($grn->status === 'Pending') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $grn->approved ? 'Approved' : ($grn->status ?? 'Pending') }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                            <a href="{{ route('grns.show', $grn->id) }}" class="text-blue-600 hover:text-blue-900 text-xs sm:text-sm font-medium">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500 py-8">
                                            <i class="fas fa-inbox text-2xl opacity-50 mb-2 block"></i>
                                            <p class="text-sm">No goods received notes found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
