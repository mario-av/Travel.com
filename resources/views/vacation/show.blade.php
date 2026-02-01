@extends('layouts.app')

@section('title', $vacation->title . ' - Travel.com')

@section('content')
{{-- Hero Image --}}
<section class="relative h-96 bg-gradient-to-br from-blue-600 to-indigo-700">
    @if($vacation->photos->count() > 0)
    <img src="{{ url('storage/' . $vacation->photos->where('is_main', true)->first()?->path ?? $vacation->photos->first()->path) }}"
        alt="{{ $vacation->title }}"
        class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-black/30"></div>
    @endif

    <div class="absolute inset-0 flex items-end">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 w-full">
            <div class="flex items-center space-x-2 mb-2">
                @if($vacation->featured)
                <span class="bg-yellow-500 text-white text-sm font-bold px-3 py-1 rounded-full">
                    <i class="bi bi-star-fill me-1"></i>Featured
                </span>
                @endif
                <span class="bg-white/90 text-gray-700 text-sm px-3 py-1 rounded-full">
                    {{ $vacation->category->name ?? 'Uncategorized' }}
                </span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">{{ $vacation->title }}</h1>
            <p class="text-xl text-white/90">
                <i class="bi bi-geo-alt me-1"></i>{{ $vacation->location }}
            </p>
        </div>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Description --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">About This Vacation</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $vacation->description }}</p>
                </div>

                {{-- Photo Gallery --}}
                @if($vacation->photos->count() > 1)
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">Photo Gallery</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($vacation->photos as $photo)
                        <img src="{{ url('storage/' . $photo->path) }}"
                            alt="Vacation photo"
                            class="rounded-lg h-32 w-full object-cover hover:opacity-90 transition cursor-pointer">
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Reviews --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">Reviews</h2>

                    @if($vacation->reviews->where('approved', true)->count() > 0)
                    <div class="space-y-4">
                        @foreach($vacation->reviews->where('approved', true) as $review)
                        <div class="border-b border-gray-100 pb-4 last:border-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="bi bi-person text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $review->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex text-yellow-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                </div>
                            </div>
                            <p class="text-gray-600">{{ $review->content }}</p>

                            {{-- Edit/Delete for owner --}}
                            @auth
                            @if(Auth::user()->id == $review->user_id)
                            <div class="mt-2 flex space-x-2">
                                <a href="{{ route('review.edit', $review) }}" class="text-sm text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('review.destroy', $review) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                            @endif
                            @endauth
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500">No reviews yet. Be the first to review this vacation!</p>
                    @endif

                    {{-- Add Review Form --}}
                    @auth
                    @if(Auth::user()->hasBookedVacation($vacation->id))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Write a Review</h3>
                        <form action="{{ route('review.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="vacation_id" value="{{ $vacation->id }}">

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                <select name="rating" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select rating</option>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
                                <textarea name="content" rows="4"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Share your experience..." required minlength="10" maxlength="1000"></textarea>
                            </div>

                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                                Submit Review
                            </button>
                        </form>
                    </div>
                    @else
                    <p class="mt-4 text-gray-500 bg-gray-50 p-4 rounded-lg">
                        <i class="bi bi-info-circle me-2"></i>You need to book this vacation before leaving a review.
                    </p>
                    @endif
                    @else
                    <p class="mt-4 text-gray-500 bg-gray-50 p-4 rounded-lg">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Log in</a> to leave a review.
                    </p>
                    @endauth
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Booking Card --}}
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                    <div class="text-center mb-4">
                        <span class="text-4xl font-bold text-blue-600">${{ number_format($vacation->price, 0) }}</span>
                        <span class="text-gray-500">/person</span>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span><i class="bi bi-calendar me-2"></i>Duration</span>
                            <span class="font-semibold">{{ $vacation->duration_days }} days</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span><i class="bi bi-calendar-date me-2"></i>Start Date</span>
                            <span class="font-semibold">{{ \Carbon\Carbon::parse($vacation->start_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span><i class="bi bi-people me-2"></i>Available Slots</span>
                            <span class="font-semibold {{ $vacation->available_slots < 5 ? 'text-red-600' : '' }}">
                                {{ $vacation->available_slots }}
                            </span>
                        </div>
                    </div>

                    @auth
                    @if(Auth::user()->hasVerifiedEmail())
                    @if(!Auth::user()->hasBookedVacation($vacation->id))
                    @if($vacation->available_slots > 0)
                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="vacation_id" value="{{ $vacation->id }}">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition">
                            <i class="bi bi-check-circle me-2"></i>Book Now
                        </button>
                    </form>
                    @else
                    <button disabled class="w-full bg-gray-400 text-white py-3 rounded-lg font-semibold cursor-not-allowed">
                        Sold Out
                    </button>
                    @endif
                    @else
                    <div class="text-center p-4 bg-green-50 rounded-lg text-green-700">
                        <i class="bi bi-check-circle-fill text-2xl mb-2"></i>
                        <p class="font-semibold">You've booked this vacation</p>
                    </div>
                    @endif
                    @else
                    <div class="text-center p-4 bg-yellow-50 rounded-lg text-yellow-700">
                        <i class="bi bi-envelope-exclamation text-2xl mb-2"></i>
                        <p class="font-semibold mb-2">Verify your email to book</p>
                        <a href="{{ route('verification.notice') }}" class="text-sm text-blue-600 hover:underline">Resend verification email</a>
                    </div>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login to Book
                    </a>
                    <p class="text-center text-sm text-gray-500 mt-2">
                        Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
                    </p>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
@endsection