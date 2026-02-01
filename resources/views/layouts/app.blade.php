<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Travel.com - Vacation Deals')</title>
    <meta name="description" content="@yield('meta_description', 'Discover amazing vacation deals and create unforgettable memories with Travel.com. Book your next adventure today.')">
    <meta name="keywords" content="@yield('meta_keywords', 'travel, vacation, holidays, booking, flight, hotel, tourism')">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Travel.com - Vacation Deals')">
    <meta property="og:description" content="@yield('meta_description', 'Discover amazing vacation deals and create unforgettable memories with Travel.com.')">
    <meta property="og:image" content="@yield('meta_image', asset('images/default-share.jpg'))">

    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'Travel.com - Vacation Deals')">
    <meta property="twitter:description" content="@yield('meta_description', 'Discover amazing vacation deals and create unforgettable memories with Travel.com.')">
    <meta property="twitter:image" content="@yield('meta_image', asset('images/default-share.jpg'))">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --accent-500: #f43f5e;
            /* Default Rose */
            --accent-600: #e11d48;
            --accent-50: #fff1f2;
        }

        .text-accent {
            color: var(--accent-500);
        }

        .bg-accent {
            background-color: var(--accent-500);
        }

        .bg-accent-hover:hover {
            background-color: var(--accent-600);
        }

        .border-accent {
            border-color: var(--accent-500);
        }

        .ring-accent:focus {
            --tw-ring-color: var(--accent-500);
        }
    </style>

    <script>
        // Apply theme from session (initial load)
        const themes = {
            rose: {
                500: '#f43f5e',
                600: '#e11d48',
                50: '#fff1f2'
            },
            indigo: {
                500: '#6366f1',
                600: '#4f46e5',
                50: '#eef2ff'
            },
            emerald: {
                500: '#10b981',
                600: '#059669',
                50: '#ecfdf5'
            },
            amber: {
                500: '#f59e0b',
                600: '#d97706',
                50: '#fffbeb'
            },
            violet: {
                500: '#8b5cf6',
                600: '#7c3aed',
                50: '#f5f3ff'
            },
            cyan: {
                500: '#06b6d4',
                600: '#0891b2',
                50: '#ecfeff'
            },
            slate: {
                500: '#64748b',
                600: '#475569',
                50: '#f8fafc'
            },
            tangerine: {
                500: '#f97316',
                600: '#ea580c',
                50: '#fff7ed'
            }
        };

        const currentTheme = "{{ session('accent_color', 'rose') }}";
        if (themes[currentTheme]) {
            document.documentElement.style.setProperty('--accent-500', themes[currentTheme][500]);
            document.documentElement.style.setProperty('--accent-600', themes[currentTheme][600]);
            document.documentElement.style.setProperty('--accent-50', themes[currentTheme][50]);
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center">
                    <a href="{{ route('main.index') }}" class="flex items-center space-x-2">
                        <i class="bi bi-airplane-engines-fill text-rose-500 text-2xl"></i>
                        <span class="text-rose-500 font-bold text-xl tracking-tighter uppercase">travel.com</span>
                    </a>
                </div>

                {{-- Navigation Links --}}
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('main.index') }}" class="text-gray-600 hover:text-rose-500 px-3 py-2 text-sm font-medium transition">
                        Home
                    </a>

                    @auth
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-rose-500 px-3 py-2 text-sm font-medium transition">
                        My Profile
                    </a>

                    @if(Auth::user()->rol === 'admin')
                    <a href="{{ route('vacation.index') }}" class="text-gray-600 hover:text-rose-500 px-3 py-2 text-sm font-medium transition">
                        Management
                    </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline-block ml-4">
                        @csrf
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm font-bold hover:bg-gray-700 transition">
                            Logout
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-rose-500 px-3 py-2 text-sm font-medium transition">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="bg-rose-500 text-white px-5 py-2 rounded-full text-sm font-bold hover:bg-rose-600 transition shadow-sm ml-2">
                        Sign up
                    </a>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-rose-500 p-2" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <i class="bi bi-list text-3xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('main.index') }}" class="block text-gray-800 font-medium px-3 py-2 rounded-lg hover:bg-gray-50">Home</a>
                @auth
                <a href="{{ route('home') }}" class="block text-gray-800 font-medium px-3 py-2 rounded-lg hover:bg-gray-50">My Profile</a>
                @if(Auth::user()->rol === 'admin')
                <a href="{{ route('vacation.index') }}" class="block text-gray-800 font-medium px-3 py-2 rounded-lg hover:bg-gray-50">Management</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left text-rose-500 font-medium px-3 py-2 rounded-lg hover:bg-gray-50">Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="block text-gray-800 font-medium px-3 py-2 rounded-lg hover:bg-gray-50">Log in</a>
                <a href="{{ route('register') }}" class="block text-rose-500 font-bold px-3 py-2 rounded-lg hover:bg-gray-50">Sign up</a>
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
    <footer class="bg-gray-900 text-white pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                {{-- About Column --}}
                <div class="space-y-6">
                    <a href="{{ route('main.index') }}" class="flex items-center space-x-2">
                        <i class="bi bi-airplane-engines-fill text-accent text-3xl"></i>
                        <span class="text-white font-bold text-2xl tracking-tighter uppercase">travel.com</span>
                    </a>
                    <p class="text-gray-400 leading-relaxed">
                        Making world-class travel experiences accessible to everyone. Explore, book, and enjoy your next adventure with confidence.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-accent transition-colors">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-accent transition-colors">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-accent transition-colors">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-100">Quick Links</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('main.index') }}" class="text-gray-400 hover:text-accent transition-colors">Explore All</a></li>
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-accent transition-colors">My Dashboard</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-accent transition-colors">Gift Cards</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-accent transition-colors">Travel Guide</a></li>
                    </ul>
                </div>

                {{-- Support --}}
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest mb-6 text-gray-100">Support</h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-accent transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-accent transition-colors">Safety Information</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-accent transition-colors">Cancellation Options</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-accent transition-colors">Report a Concern</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="space-y-6">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-100">Stay Updated</h3>
                    <p class="text-gray-400 text-sm">Join our newsletter to get the best travel deals first.</p>
                    <form action="#" method="POST" class="relative">
                        <input type="email" placeholder="Email address"
                            class="w-full bg-gray-800 border-none rounded-xl py-3 px-4 text-sm text-gray-100 focus:ring-2 focus:ring-accent outline-none">
                        <button type="submit" class="absolute right-1 top-1 bottom-1 bg-accent hover:bg-accent-hover text-white px-4 rounded-lg transition-colors">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Travel.com Inc. &bull; <a href="#" class="hover:underline">Privacy</a> &bull; <a href="#" class="hover:underline">Terms</a> &bull; <a href="#" class="hover:underline">Sitemap</a></p>
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <span class="flex items-center"><i class="bi bi-globe me-2"></i> English (US)</span>
                    <span class="flex items-center font-bold text-gray-300">$ USD</span>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
    {{-- Global Loader --}}
    <div id="global-loader" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center transition-opacity duration-300 opacity-0">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-rose-500"></div>
            <p class="mt-4 text-rose-500 font-semibold animate-pulse">Loading...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('global-loader');

            const showLoader = () => {
                loader.classList.remove('hidden');
                requestAnimationFrame(() => {
                    loader.classList.remove('opacity-0');
                });
            };

            const hideLoader = () => {
                loader.classList.add('opacity-0');
                setTimeout(() => {
                    loader.classList.add('hidden');
                }, 300);
            };

            // Hide loader on initial page load (in case it was stuck)
            window.addEventListener('load', hideLoader);

            // Global Click Listener (Event Delegation)
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');

                if (!link) return;

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');

                // Logic to decide if we show loader
                if (href &&
                    href !== '#' &&
                    !href.startsWith('javascript:') &&
                    !href.startsWith('mailto:') &&
                    !href.startsWith('tel:') &&
                    (href.startsWith('/') || href.startsWith(window.location.origin) || !href.startsWith('http')) && // Internal links
                    target !== '_blank' &&
                    !e.ctrlKey && !e.metaKey && !e.shiftKey && !e.altKey // Not opening in new tab
                ) {
                    showLoader();
                }
            });

            // Form Submissions
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!e.defaultPrevented && form.checkValidity()) {
                    showLoader();
                }
            });

            // Handle Back/Forward Cache (restores page from cache)
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    hideLoader();
                }
            });
        });
    </script>
</body>

</html>