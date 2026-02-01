@extends('layouts.app')

@section('title', 'My Dashboard - traveldotcom')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen" x-data="{ activeTab: '{{ $tab ?? 'overview' }}' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Profile Header --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="h-32 bg-accent opacity-90 transition-colors"></div>
            <div class="px-8 pb-8">
                <div class="relative flex flex-col md:flex-row justify-between items-center md:items-end -mt-12 space-y-4 md:space-y-0 text-center md:text-left">
                    <div class="flex flex-col md:flex-row items-center md:items-end space-y-4 md:space-y-0 md:space-x-6">
                        <div class="group relative w-24 h-24 bg-white rounded-2xl shadow-lg p-1 overflow-hidden">
                            @if(Auth::user()->profile_photo_path)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" class="w-full h-full object-cover rounded-xl">
                            @else
                            <div class="w-full h-full bg-accent text-white opacity-20 flex items-center justify-center -mb-[100%] rounded-xl"></div>
                            <div class="relative w-full h-full flex items-center justify-center text-accent text-3xl font-bold rounded-xl border-2 border-accent/20">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            @endif
                        </div>
                        <div class="pb-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ Auth::user()->name }}</h1>
                            <p class="text-gray-500 font-medium">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3 pb-1">
                        <span class="px-4 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold uppercase tracking-wider">
                            {{ Auth::user()->rol }}
                        </span>
                        @if(Auth::user()->hasVerifiedEmail())
                        <span class="px-4 py-1.5 bg-green-100 text-green-600 rounded-full text-xs font-bold uppercase tracking-wider flex items-center">
                            <i class="bi bi-patch-check-fill me-1.5"></i>Verified
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Dashboard Navigation (Tabs) --}}
                <div class="flex items-center justify-center md:justify-start space-x-8 mt-10 border-b border-gray-100 overflow-x-auto no-scrollbar">
                    <button @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'border-accent text-accent' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="pb-4 border-b-2 font-bold text-sm transition-all duration-200 whitespace-nowrap">
                        <i class="bi bi-grid-1x2 me-2"></i>Overview
                    </button>
                    <button @click="activeTab = 'reviews'"
                        :class="activeTab === 'reviews' ? 'border-accent text-accent' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="pb-4 border-b-2 font-bold text-sm transition-all duration-200 whitespace-nowrap">
                        <i class="bi bi-chat-left-text me-2"></i>My Reviews
                    </button>
                    <button @click="activeTab = 'settings'"
                        :class="activeTab === 'settings' ? 'border-accent text-accent' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="pb-4 border-b-2 font-bold text-sm transition-all duration-200 whitespace-nowrap">
                        <i class="bi bi-gear me-2"></i>Account Settings
                    </button>
                </div>
            </div>
        </div>

        {{-- Tab Content --}}
        <div>
            {{-- Overview Tab --}}
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
                {{-- Stats Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-sm">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ Auth::user()->bookings->count() }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-xl shadow-sm">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Reviews Written</p>
                            <p class="text-2xl font-bold text-gray-900">{{ Auth::user()->reviews->count() }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-sm">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Member Since</p>
                            <p class="text-2xl font-bold text-gray-900">{{ Auth::user()->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Bookings Section --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white">
                        <h2 class="text-xl font-bold text-gray-900">Recent Bookings</h2>
                        <a href="{{ route('main.index') }}" class="text-rose-500 hover:text-rose-600 font-bold text-sm flex items-center group transition">
                            Book New <i class="bi bi-arrow-right ms-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <div class="p-8">
                        @forelse(Auth::user()->bookings->sortByDesc('created_at') as $booking)
                        <div class="group flex flex-col sm:flex-row items-center justify-between p-6 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-xl hover:scale-[1.01] transition-all duration-300 border-2 border-transparent hover:border-rose-100 mb-4 last:mb-0">
                            <div class="flex items-center space-x-6 w-full sm:w-auto">
                                <div class="w-20 h-20 rounded-xl overflow-hidden shadow-sm flex-shrink-0">
                                    @if($booking->vacation && $booking->vacation->photos->count() > 0)
                                    <img src="{{ $booking->vacation->photos->first()->url }}" alt="{{ $booking->vacation->title }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                        <i class="bi bi-image text-2xl"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-gray-900 group-hover:text-rose-600 transition-colors">
                                        {{ $booking->vacation->title ?? 'Deleted Vacation' }}
                                    </h4>
                                    <p class="text-gray-500 font-medium flex items-center text-sm">
                                        <i class="bi bi-calendar3 me-2 text-rose-400"></i>
                                        Booked: {{ $booking->created_at->format('M d, Y') }}
                                    </p>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded-full shadow-sm
                                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : 
                                               ($booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                            {{ $booking->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 sm:mt-0 flex flex-col items-end">
                                <p class="text-2xl font-black text-rose-500">${{ number_format($booking->vacation->price ?? 0, 0) }}</p>
                                @if($booking->vacation)
                                <a href="{{ route('vacation.show', $booking->vacation) }}" class="mt-2 py-2 px-4 bg-white text-gray-600 border border-gray-200 rounded-xl text-sm font-bold shadow-sm hover:shadow-md hover:border-rose-200 hover:text-rose-600 transition-all">
                                    Details <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                <i class="bi bi-luggage text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No bookings yet</h3>
                            <p class="text-gray-500 mb-8">Ready to see the world? Start by browsing our deals.</p>
                            <a href="{{ route('main.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-8 py-3 rounded-2xl font-bold transition shadow-lg shadow-rose-200">
                                Find My Next Vacation
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Reviews Tab --}}
            <div x-show="activeTab === 'reviews'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white">
                        <h2 class="text-xl font-bold text-gray-900">Experience Logs</h2>
                        <span class="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tight">Total: {{ Auth::user()->reviews->count() }}</span>
                    </div>

                    <div class="p-8">
                        @forelse(Auth::user()->reviews->sortByDesc('created_at') as $review)
                        <div class="p-6 bg-gray-50 rounded-2xl mb-6 last:mb-0 border-l-4 border-rose-400">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-bold text-lg text-gray-900">{{ $review->vacation->title ?? 'Deleted Vacation' }}</h4>
                                <div class="flex text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} me-0.5"></i>
                                        @endfor
                                </div>
                            </div>
                            <p class="text-gray-600 font-medium leading-relaxed italic mb-4">"{{ $review->content }}"</p>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-200/50">
                                <div class="flex items-center space-x-4">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $review->created_at->format('M d, Y') }}</span>
                                    <span class="flex items-center text-xs font-black uppercase tracking-widest {{ $review->approved ? 'text-green-600' : 'text-amber-600' }}">
                                        <i class="bi bi-{{ $review->approved ? 'patch-check-fill' : 'hourglass-split' }} me-1.5"></i>
                                        {{ $review->approved ? 'Published' : 'Under Review' }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('review.edit', $review) }}" class="p-2 transition text-gray-400 hover:text-blue-500 hover:bg-white rounded-lg"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('review.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete review?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 transition text-gray-400 hover:text-rose-500 hover:bg-white rounded-lg"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-200">
                                <i class="bi bi-chat-quote text-3xl"></i>
                            </div>
                            <p class="text-gray-400 font-bold">Your reviews will appear here once you've visited a destination!</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Settings Tab --}}
            <div x-show="activeTab === 'settings'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl mx-auto">
                    <div class="px-8 py-6 border-b border-gray-50 bg-white">
                        <h2 class="text-xl font-bold text-gray-900">Profile & Security</h2>
                        <p class="text-gray-500 text-sm">Update your personal information and login credentials.</p>
                    </div>

                    <form method="POST" action="{{ route('home.update') }}" enctype="multipart/form-data" class="p-8 space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4">App Customization</label>
                            <div class="bg-gray-50 rounded-2xl p-6">
                                <p class="text-sm font-bold text-gray-700 mb-4">Accent Color</p>
                                <div class="grid grid-cols-4 sm:grid-cols-8 gap-4">
                                    <button type="button" onclick="setAppTheme('rose')" class="w-10 h-10 rounded-full bg-[#f43f5e] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color ?? 'rose') == 'rose' ? 'ring-rose-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                    <button type="button" onclick="setAppTheme('indigo')" class="w-10 h-10 rounded-full bg-[#6366f1] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color) == 'indigo' ? 'ring-indigo-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                    <button type="button" onclick="setAppTheme('emerald')" class="w-10 h-10 rounded-full bg-[#10b981] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color) == 'emerald' ? 'ring-emerald-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                    <button type="button" onclick="setAppTheme('amber')" class="w-10 h-10 rounded-full bg-[#f59e0b] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color) == 'amber' ? 'ring-amber-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                    <button type="button" onclick="setAppTheme('violet')" class="w-10 h-10 rounded-full bg-[#8b5cf6] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color) == 'violet' ? 'ring-violet-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                    <button type="button" onclick="setAppTheme('cyan')" class="w-10 h-10 rounded-full bg-[#06b6d4] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color) == 'cyan' ? 'ring-cyan-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                    <button type="button" onclick="setAppTheme('slate')" class="w-10 h-10 rounded-full bg-[#64748b] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color) == 'slate' ? 'ring-slate-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                    <button type="button" onclick="setAppTheme('tangerine')" class="w-10 h-10 rounded-full bg-[#f97316] ring-2 ring-offset-2 {{ (session('accent_color') ?? Auth::user()->accent_color) == 'tangerine' ? 'ring-orange-500' : 'ring-transparent' }} hover:scale-110 transition"></button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Display Name</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                class="w-full bg-gray-50 border-2 border-transparent focus:border-accent focus:bg-white rounded-2xl px-5 py-3 outline-none transition-all duration-200 font-bold text-gray-800">
                            @error('name') <p class="text-accent text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Email Identity</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                class="w-full bg-gray-50 border-2 border-transparent focus:border-accent focus:bg-white rounded-2xl px-5 py-3 outline-none transition-all duration-200 font-bold text-gray-800">
                            @error('email') <p class="text-accent text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-4">
                            <div class="bg-amber-50 rounded-2xl p-4 flex items-start space-x-3 mb-6 border border-amber-100">
                                <i class="bi bi-shield-lock-fill text-amber-500 text-xl"></i>
                                <div>
                                    <p class="text-amber-800 text-sm font-bold">Security Verification</p>
                                    <p class="text-amber-700 text-xs text-pretty">Enter your current password to save changes.</p>
                                </div>
                            </div>
                            <input type="password" name="current-password" required placeholder="Current Password"
                                class="w-full bg-gray-50 border-2 border-transparent focus:border-accent focus:bg-white rounded-2xl px-5 py-3 outline-none transition-all duration-200 font-bold text-gray-800">
                            @error('current-password') <p class="text-accent text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>
                        {{-- Form grid --}}
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Profile Photo</label>
                                <div class="flex items-center space-x-6 bg-gray-50 p-4 rounded-2xl border-2 border-dashed border-gray-200 hover:border-accent group transition-colors">
                                    <div class="w-16 h-16 rounded-xl bg-white shadow-sm overflow-hidden flex-shrink-0">
                                        @if(Auth::user()->profile_photo_path)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="bi bi-person-fill text-2xl"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="profile_photo" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-accent/10 file:text-accent hover:file:bg-accent/20 cursor-pointer">
                                        <p class="text-[10px] text-gray-400 mt-2">JPG, PNG or GIF. Max 2MB.</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">New Password (Opt)</label>
                                <input type="password" name="password" placeholder="••••••••"
                                    class="w-full bg-gray-50 border-2 border-transparent focus:border-accent focus:bg-white rounded-2xl px-5 py-3 outline-none transition-all duration-200 font-bold text-gray-800">
                                @error('password') <p class="text-accent text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Confirm New</label>
                                <input type="password" name="password_confirmation" placeholder="••••••••"
                                    class="w-full bg-gray-50 border-2 border-transparent focus:border-accent focus:bg-white rounded-2xl px-5 py-3 outline-none transition-all duration-200 font-bold text-gray-800">
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white py-4 rounded-2xl font-black uppercase tracking-widest transition-all shadow-lg active:scale-[0.98]">
                                <i class="bi bi-check2-circle me-2"></i>Secure Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function setAppTheme(color) {
        fetch('{{ route("theme.accent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    accent_color: color
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Live update variables
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

                    const theme = themes[color];
                    if (theme) {
                        document.documentElement.style.setProperty('--accent-500', theme[500]);
                        document.documentElement.style.setProperty('--accent-600', theme[600]);
                        document.documentElement.style.setProperty('--accent-50', theme[50]);
                    }

                    // Remove active ring from all buttons
                    document.querySelectorAll('button[onclick^="setAppTheme"]').forEach(btn => {
                        btn.className = btn.className.replace(/ring-[a-z]+-500/g, 'ring-transparent');
                    });

                    // Add active ring to clicked button
                    const ringClasses = {
                        rose: 'ring-rose-500',
                        indigo: 'ring-indigo-500',
                        emerald: 'ring-emerald-500',
                        amber: 'ring-amber-500',
                        violet: 'ring-violet-500',
                        cyan: 'ring-cyan-500',
                        slate: 'ring-slate-500',
                        tangerine: 'ring-orange-500'
                    };

                    const eventTarget = window.event.currentTarget;
                    eventTarget.classList.remove('ring-transparent');
                    eventTarget.classList.add(ringClasses[color]);
                }
            });
    }
</script>
@endsection