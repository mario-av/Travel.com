@extends('layouts.app')

@section('title', $vacation->title . ' - traveldotcom')

@section('content')
{{-- Hero Image --}}
<section class="relative h-96 bg-gradient-to-br from-blue-600 to-indigo-700">
    @if($vacation->photos->count() > 0)
    @php
    $mainPhoto = $vacation->photos->where('is_main', true)->first() ?? $vacation->photos->first();
    @endphp
    <img src="{{ $mainPhoto->url }}"
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
                        <img src="{{ $photo->url }}"
                            alt="Vacation photo"
                            class="rounded-lg h-32 w-full object-cover hover:opacity-90 transition cursor-pointer">
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Reviews --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">Reviews</h2>

                    {{-- Pending Reviews (Admin Only) --}}
                    @if(Auth::check() && Auth::user()->isAdmin() && $vacation->reviews->where('approved', false)->count() > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h3 class="text-yellow-800 font-semibold mb-3">
                            <i class="bi bi-hourglass-split me-2"></i>Pending Approval ({{ $vacation->reviews->where('approved', false)->count() }})
                        </h3>
                        <div class="space-y-4">
                            @foreach($vacation->reviews->where('approved', false) as $review)
                            <div class="bg-white p-3 rounded shadow-sm border border-yellow-100">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $review->user->name }} <span class="text-xs text-gray-500">({{ $review->created_at->diffForHumans() }})</span></p>
                                        <div class="flex text-yellow-500 text-xs mb-1">
                                            @for($i = 1; $i <= 5; $i++) <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i> @endfor
                                        </div>
                                        <p class="text-gray-600 text-sm">{{ $review->content }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form action="{{ route('admin.review.approve', $review) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-700 text-sm font-semibold" title="Approve">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('review.destroy', $review) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-semibold" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @php
                    $displayReviews = $vacation->reviews->filter(function($review) {
                    return $review->approved || (Auth::check() && Auth::id() == $review->user_id);
                    })->sortByDesc('created_at');
                    @endphp

                    @if($displayReviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($displayReviews as $review)
                        <div class="border-b border-gray-100 pb-4 last:border-0 {{ !$review->approved ? 'bg-yellow-50/50 p-4 rounded-lg border border-yellow-100' : '' }}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="bi bi-person text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">
                                            {{ $review->user->name }}
                                            @if(!$review->approved)
                                            <span class="ms-2 text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter">
                                                Pending Approval
                                            </span>
                                            @endif
                                        </p>
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

                            {{-- Edit/Delete/History for owner --}}
                            <div class="mt-2 flex items-center space-x-3 text-sm">
                                @auth
                                @if(Auth::user()->id == $review->user_id)
                                <a href="{{ route('review.edit', $review) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('review.destroy', $review) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                                @endif
                                @endauth

                                @if($review->histories->count() > 0)
                                <button onclick="document.getElementById('history-{{ $review->id }}').classList.toggle('hidden')" class="text-gray-500 hover:text-gray-700 flex items-center">
                                    <i class="bi bi-clock-history me-1"></i>History ({{ $review->histories->count() }})
                                </button>
                                @endif
                            </div>

                            @if($review->histories->count() > 0)
                            <div id="history-{{ $review->id }}" class="hidden mt-3 space-y-3 pl-4 border-l-2 border-gray-100 italic text-sm text-gray-500">
                                @foreach($review->histories as $history)
                                <div class="relative pb-2">
                                    <div class="flex justify-between items-center mb-1">
                                        <div class="flex text-yellow-500 text-[10px]">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $history->rating ? '-fill' : '' }}"></i>
                                                @endfor
                                        </div>
                                        <span class="text-[10px]">{{ $history->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p>{{ $history->content }}</p>
                                </div>
                                @endforeach
                            </div>
                            @endif
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
                                <select name="rating" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('rating') border-red-500 @enderror" required>
                                    <option value="">Select rating</option>
                                    <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>5 - Excellent</option>
                                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 - Very Good</option>
                                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 - Good</option>
                                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 - Fair</option>
                                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 - Poor</option>
                                </select>
                                @error('rating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
                                <textarea name="comment" rows="4"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('comment') border-red-500 @enderror"
                                    placeholder="Share your experience..." required minlength="10" maxlength="1000">{{ old('comment') }}</textarea>
                                @error('comment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
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
                <div class="bg-white rounded-xl shadow-[0_6px_16px_rgba(0,0,0,0.12)] border border-gray-200 p-6 sticky top-24">
                    <div class="flex justify-between items-end mb-6 border-b border-gray-100 pb-4">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($vacation->price, 0) }}</span>
                            <span class="text-gray-500 text-base"> / night</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="bi bi-star-fill text-rose-500 mr-1"></i>
                            <span class="font-semibold text-gray-900">
                                {{ $vacation->reviews_count > 0 ? number_format($vacation->average_rating, 2) : 'New' }}
                            </span>
                            <span class="mx-1">·</span>
                            <span class="underline text-gray-500">{{ $vacation->reviews_count }} Reviews</span>
                        </div>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <div class="flex border-b border-gray-300">
                                <div class="flex-1 p-3 border-r border-gray-300">
                                    <label class="block text-xs font-bold text-gray-800 uppercase">Check-in</label>
                                    <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($vacation->start_date)->format('m/d/Y') }}</div>
                                </div>
                                <div class="flex-1 p-3">
                                    <label class="block text-xs font-bold text-gray-800 uppercase">Checkout</label>
                                    <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($vacation->start_date)->addDays($vacation->duration_days)->format('m/d/Y') }}</div>
                                </div>
                            </div>
                            <div class="p-3">
                                <label class="block text-xs font-bold text-gray-800 uppercase">Guests</label>
                                <div class="text-sm text-gray-600">{{ $vacation->available_slots }} slots left</div>
                            </div>
                        </div>
                    </div>

                    @auth
                    @if(Auth::user()->hasVerifiedEmail())
                    @if(!Auth::user()->hasBookedVacation($vacation->id))
                    @if($vacation->available_slots > 0)
                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="vacation_id" value="{{ $vacation->id }}">

                        <div class="mb-4">
                            <label for="num_guests" class="block text-xs font-bold text-gray-800 uppercase mb-1">Number of Guests</label>
                            <select name="num_guests" id="num_guests" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none">
                                @for($i = 1; $i <= min(10, $vacation->available_slots); $i++)
                                    <option value="{{ $i }}">{{ $i }} guest{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white py-3.5 rounded-lg font-bold text-lg transition shadow-md">
                            Reserve
                        </button>
                    </form>
                    <p class="text-center text-gray-500 text-sm mt-3">You won't be charged yet</p>
                    @else
                    <button disabled class="w-full bg-gray-200 text-gray-400 py-3 rounded-lg font-semibold cursor-not-allowed">
                        Sold Out
                    </button>
                    @endif
                    @else
                    <div class="text-center p-4 bg-green-50 rounded-lg border border-green-100 text-green-800">
                        <i class="bi bi-check-circle-fill text-xl mb-2 text-green-600"></i>
                        <p class="font-bold">You're going!</p>
                        <p class="text-sm">Reservation confirmed.</p>
                    </div>
                    @endif
                    @else
                    <div class="text-center p-4 bg-yellow-50 rounded-lg text-yellow-800 border border-yellow-100">
                        <i class="bi bi-envelope-exclamation text-xl mb-2"></i>
                        <p class="font-bold">Email Verification Needed</p>
                        <a href="{{ route('verification.notice') }}" class="text-sm font-semibold underline hover:text-yellow-900">Resend email</a>
                    </div>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="block w-full text-center bg-rose-500 hover:bg-rose-600 text-white py-3 rounded-lg font-bold transition">
                        Log in to Reserve
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