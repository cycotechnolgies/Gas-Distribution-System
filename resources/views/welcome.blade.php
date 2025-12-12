<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gas LK | Energy for the Future</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Navigation -->
    <nav class="w-full bg-white shadow-sm fixed top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            <div class="flex items-center space-x-3">
                <img src="/images/logo.png" alt="Gas LK Logo">
            </div>

            <div class="flex items-center space-x-5">
                <a href="{{ route('login') }}"
                   class="text-gray-700 font-medium hover:text-gray-900 transition">
                    Login
                </a>

                <a href="{{ route('register') }}"
                   class="px-5 py-2 bg-yellow-500 text-white rounded-md font-semibold shadow hover:bg-yellow-600 transition">
                    Create Account
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center">

            <div class="w-full md:w-1/2 mb-10 md:mb-0">
                <h1 class="text-5xl font-extrabold text-gray-900 leading-tight">
                    Powering Sri Lanka<br>
                    with Reliable Energy.
                </h1>

                <p class="mt-6 text-lg text-gray-600">
                    Gas LK delivers clean, safe and dependable LPG solutions for homes and businesses.
                    Join the nationwide network trusted for stability and performance.
                </p>

                <div class="mt-8 flex space-x-4">
                    <a href="{{ route('login') }}"
                       class="px-6 py-3 bg-gray-900 text-white rounded-lg shadow hover:bg-black transition font-semibold">
                        Login
                    </a>

                    <a href="{{ route('register') }}"
                       class="px-6 py-3 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 transition font-semibold">
                        Register
                    </a>
                </div>
            </div>

            <div class="w-full md:w-1/2 flex justify-center">
                <div class="w-80 h-80 flex items-center justify-center">
                    <img src="/images/logo.png" alt="Gas LK Logo">
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="py-6 border-t bg-white">
        <div class="max-w-7xl mx-auto px-6 text-center text-gray-500">
            © {{ date('Y') }} Gas LK. All rights reserved.
        </div>
    </footer>

</body>
</html>
