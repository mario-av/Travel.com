<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Travel.com - Vacation Deals')</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @yield('styles')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Navigation --}}
    <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center">
                    <a href="{{ route('main.index') }}" class="flex items-center space-x-2">
                        <i class="bi bi-airplane text-white text-2xl"></i>
                        <span class="text-white font-bold text-xl">Travel.com</span>
                    </a>
                </div>

                {{-- Navigation Links --}}
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('main.index') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md transition">
                        <i class="bi bi-house-door me-1"></i> Home
                    </a>

                    @auth
                    <a href="{{ route('home') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md transition">
                        <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                    </a>

                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('vacation.index') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md transition">
                        <i class="bi bi-gear me-1"></i> Admin
                    </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:bg-white/10 px-3 py-2 rounded-md transition">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="text-white hover:bg-white/10 px-3 py-2 rounded-md transition">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-md font-semibold transition">
                        Register
                    </a>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-white" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-blue-700 pb-4">
            <div class="px-4 space-y-2">
                <a href="{{ route('main.index') }}" class="block text-white hover:bg-white/10 px-3 py-2 rounded-md">Home</a>
                @auth
                <a href="{{ route('home') }}" class="block text-white hover:bg-white/10 px-3 py-2 rounded-md">My Account</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left text-white hover:bg-white/10 px-3 py-2 rounded-md">Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="block text-white hover:bg-white/10 px-3 py-2 rounded-md">Login</a>
                <a href="{{ route('register') }}" class="block text-white hover:bg-white/10 px-3 py-2 rounded-md">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('general'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('general') }}
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        </div>
        @endif

        @error('general')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
        </div>
        @enderror
    </div>

    {{-- Modals --}}
    @yield('modal')

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Travel.com</h3>
                    <p class="text-gray-400">Discover amazing vacation deals and create unforgettable memories.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('main.index') }}" class="hover:text-white transition">All Vacations</a></li>
                        @auth
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">My Bookings</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-gray-400">
                        <i class="bi bi-envelope me-2"></i>support@travel.com
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-4 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Travel.com. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>

</html>