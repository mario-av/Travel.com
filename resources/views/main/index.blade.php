@extends('layouts.app')

@section('title', 'Travel.com - Explore Vacation Deals')

@section('modal')
{{-- Order Modal --}}
<div id="orderModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Sort by...</h3>
            <button onclick="document.getElementById('orderModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="space-y-2">
            <a href="{{ route('main.index', array_merge(['campo' => 'recent', 'orden' => 'desc'], request()->except(['page','campo','orden']))) }}"
                class="block w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-md transition">
                <i class="bi bi-clock me-2"></i>Most Recent
            </a>
            <a href="{{ route('main.index', array_merge(['campo' => 'price', 'orden' => 'asc'], request()->except(['page','campo','orden']))) }}"
                class="block w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-md transition">
                <i class="bi bi-arrow-up me-2"></i>Price: Low to High
            </a>
            <a href="{{ route('main.index', array_merge(['campo' => 'price', 'orden' => 'desc'], request()->except(['page','campo','orden']))) }}"
                class="block w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-md transition">
                <i class="bi bi-arrow-down me-2"></i>Price: High to Low
            </a>
            <a href="{{ route('main.index', array_merge(['campo' => 'title', 'orden' => 'asc'], request()->except(['page','campo','orden']))) }}"
                class="block w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-md transition">
                <i class="bi bi-sort-alpha-down me-2"></i>Title: A-Z
            </a>
        </div>
    </div>
</div>

{{-- Filter Modal --}}
<div id="filterModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Filter by...</h3>
            <button onclick="document.getElementById('filterModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('main.index') }}" method="get">
            <input type="hidden" name="campo" value="{{ $campo ?? 'recent' }}">
            <input type="hidden" name="orden" value="{{ $orden ?? 'desc' }}">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" @if(($category_id ?? null)==$cat->id) selected @endif>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price Range</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" name="priceMin" value="{{ $priceMin ?? '' }}" placeholder="Min"
                                class="w-full border border-gray-300 rounded-md pl-7 pr-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" name="priceMax" value="{{ $priceMax ?? '' }}" placeholder="Max"
                                class="w-full border border-gray-300 rounded-md pl-7 pr-3 py-2 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" value="{{ $location ?? '' }}" placeholder="Country or city..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex space-x-2 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                        <i class="bi bi-funnel me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('main.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('content')
{{-- Hero Section --}}
<section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Discover Your Perfect Vacation</h1>
        <p class="text-xl text-blue-100 mb-8">Explore amazing destinations at unbeatable prices</p>

        {{-- Search Bar --}}
        <form action="{{ route('main.index') }}" method="get" class="max-w-2xl mx-auto">
            @foreach(request()->except(['page','q']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div class="flex">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search destinations, vacations..."
                    class="flex-1 px-4 py-3 rounded-l-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 px-6 py-3 rounded-r-lg font-semibold transition">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
        </form>
    </div>
</section>

{{-- Toolbar Section --}}
<section class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-center justify-between">
            <p class="text-gray-600">
                <span class="font-semibold">{{ $vacations->total() }}</span> vacations found
            </p>
            <div class="flex space-x-2">
                <button onclick="document.getElementById('orderModal').classList.remove('hidden');document.getElementById('orderModal').classList.add('flex')"
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                    <i class="bi bi-sort-down me-1"></i>Sort
                </button>
                <button onclick="document.getElementById('filterModal').classList.remove('hidden');document.getElementById('filterModal').classList.add('flex')"
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Vacation Grid --}}
<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($vacations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vacations as $vacation)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                {{-- Image --}}
                <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-500 relative">
                    @if($vacation->photos->count() > 0)
                    <img src="{{ url('storage/' . $vacation->photos->first()->path) }}"
                        alt="{{ $vacation->title }}"
                        class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="bi bi-image text-white text-4xl opacity-50"></i>
                    </div>
                    @endif

                    @if($vacation->featured)
                    <span class="absolute top-2 left-2 bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded">
                        <i class="bi bi-star-fill me-1"></i>Featured
                    </span>
                    @endif

                    <span class="absolute bottom-2 right-2 bg-white/90 text-blue-600 font-bold px-3 py-1 rounded-lg">
                        ${{ number_format($vacation->price, 0) }}
                    </span>
                </div>

                {{-- Content --}}
                <div class="p-4">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $vacation->location }}
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-1">{{ $vacation->title }}</h3>

                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($vacation->description, 100) }}</p>

                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <span><i class="bi bi-calendar me-1"></i>{{ $vacation->duration_days }} days</span>
                        <span><i class="bi bi-people me-1"></i>{{ $vacation->available_slots }} slots</span>
                    </div>

                    <a href="{{ route('vacation.show', $vacation) }}"
                        class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $vacations->onEachSide(2)->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <i class="bi bi-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No vacations found</h3>
            <p class="text-gray-500 mb-4">Try adjusting your search or filters</p>
            <a href="{{ route('main.index') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Clear Filters
            </a>
        </div>
        @endif
    </div>
</section>
@endsection