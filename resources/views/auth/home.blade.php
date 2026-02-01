@extends('layouts.app')

@section('title', 'My Dashboard - Travel.com')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Profile Card --}}
                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-3xl font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-500">{{ Auth::user()->email }}</p>

                    <div class="mt-4 flex justify-center space-x-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">
                            {{ ucfirst(Auth::user()->rol) }}
                        </span>
                        @if(Auth::user()->hasVerifiedEmail())
                        <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm">
                            <i class="bi bi-check-circle me-1"></i>Verified
                        </span>
                        @else
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-sm">
                            <i class="bi bi-exclamation-circle me-1"></i>Unverified
                        </span>
                        @endif
                    </div>

                    <a href="{{ route('home.edit') }}" class="block mt-4 text-blue-600 hover:underline">
                        <i class="bi bi-pencil me-1"></i>Edit Profile
                    </a>
                </div>

                {{-- Quick Stats --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600"><i class="bi bi-calendar-check me-2"></i>Total Bookings</span>
                            <span class="font-bold text-blue-600">{{ Auth::user()->bookings->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600"><i class="bi bi-chat-dots me-2"></i>Reviews Written</span>
                            <span class="font-bold text-blue-600">{{ Auth::user()->reviews->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Email Verification Alert --}}
                @if(!Auth::user()->hasVerifiedEmail())
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <i class="bi bi-envelope-exclamation text-yellow-600 text-2xl me-4"></i>
                        <div>
                            <h3 class="font-semibold text-yellow-800">Verify Your Email</h3>
                            <p class="text-yellow-700 mb-3">Please verify your email address to book vacations.</p>
                            <form action="{{ route('verification.resend') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                    Resend Verification Email
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- My Bookings --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold">My Bookings</h2>
                        <a href="{{ route('main.index') }}" class="text-blue-600 hover:underline text-sm">
                            <i class="bi bi-plus me-1"></i>Book New Vacation
                        </a>
                    </div>

                    @if(Auth::user()->bookings->count() > 0)
                    <div class="space-y-4">
                        @foreach(Auth::user()->bookings as $booking)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center">
                                    @if($booking->vacation && $booking->vacation->photos->count() > 0)
                                    <img src="{{ url('storage/' . $booking->vacation->photos->first()->path) }}"
                                        alt="{{ $booking->vacation->title }}"
                                        class="w-full h-full object-cover rounded-lg">
                                    @else
                                    <i class="bi bi-image text-white text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">
                                        {{ $booking->vacation->title ?? 'Deleted Vacation' }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        <i class="bi bi-calendar me-1"></i>
                                        Booked: {{ $booking->created_at->format('M d, Y') }}
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full
                                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-600' : 
                                               ($booking->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600">${{ number_format($booking->vacation->price ?? 0, 0) }}</p>
                                @if($booking->vacation)
                                <a href="{{ route('vacation.show', $booking->vacation) }}" class="text-sm text-gray-500 hover:text-blue-600">
                                    View <i class="bi bi-arrow-right"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="bi bi-calendar-x text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 mb-4">You haven't booked any vacations yet.</p>
                        <a href="{{ route('main.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                            Explore Vacations
                        </a>
                    </div>
                    @endif
                </div>

                {{-- My Reviews --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">My Reviews</h2>

                    @if(Auth::user()->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach(Auth::user()->reviews as $review)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-800">
                                    {{ $review->vacation->title ?? 'Deleted Vacation' }}
                                </h4>
                                <div class="flex text-yellow-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($review->content, 100) }}</p>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                                <span class="{{ $review->approved ? 'text-green-600' : 'text-yellow-600' }}">
                                    <i class="bi bi-{{ $review->approved ? 'check-circle' : 'clock' }} me-1"></i>
                                    {{ $review->approved ? 'Approved' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">You haven't written any reviews yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection