<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gas LK</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-40 hidden z-40 md:hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar"
               class="fixed md:static inset-y-0 left-0 transform -translate-x-full md:translate-x-0
                      w-64 bg-white shadow-md z-50 transition-transform duration-300">

            <div class="p-4 text-xl font-bold text-blue-600 rounded-lg bg-blue-100 m-2 flex gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cylinder-icon lucide-cylinder"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/></svg>
                Gas LK
            </div>

            <nav class="mt-6 flex flex-col space-y-2 px-4">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded hover:bg-indigo-100">Dashboard</a>
                <a href="/suppliers" class="px-4 py-2 rounded hover:bg-indigo-100">Suppliers</a>
                <a href="/gas-types" class="px-4 py-2 rounded hover:bg-indigo-100">Gas Types</a>
                <a href="/purchase-orders" class="px-4 py-2 rounded hover:bg-indigo-100">Purchase Orders</a>
                <a href="/grns" class="px-4 py-2 rounded hover:bg-indigo-100">GRNs</a>
                <a href="/customers" class="px-4 py-2 rounded hover:bg-indigo-100">Customers</a>
                <a href="/orders" class="px-4 py-2 rounded hover:bg-indigo-100">Orders</a>
                <a href="/routes" class="px-4 py-2 rounded hover:bg-indigo-100">Delivery Routes</a>
                <a href="/drivers" class="px-4 py-2 rounded hover:bg-indigo-100">Staff</a>
                <a href="/vehicles" class="px-4 py-2 rounded hover:bg-indigo-100">Vehicles</a>
            </nav>
        </aside>

        <!-- Main Section -->
        <div class="flex-1 flex flex-col">

            <!-- Header -->
            <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">

                <!-- Mobile Menu Button -->
                <button id="menuBtn" class="md:hidden text-gray-600 focus:outline-none">
                    ☰
                </button>

                <h1 class="text-lg font-semibold text-gray-700">
                    Dashboard
                </h1>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-red-600 px-4 py-2 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                    </button>
                </form>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>

    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('menuBtn');

        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
