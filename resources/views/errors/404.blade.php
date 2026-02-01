@extends('layouts.app')

@section('title', 'Page Not Found - traveldotcom')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center max-w-lg w-full">
        <div class="relative mb-8">
            <h1 class="text-9xl font-black text-gray-100 uppercase tracking-tighter">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-compass text-rose-500 text-6xl animate-pulse"></i>
            </div>
        </div>

        <h2 class="text-3xl font-bold text-gray-900 mb-4">You seem to be lost</h2>
        <p class="text-gray-500 mb-10 leading-relaxed">
            The destination you're looking for doesn't exist or has been moved. Let's get you back on track for your next adventure.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('main.index') }}" class="w-full sm:w-auto px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl font-bold transition-all shadow-lg active:scale-95">
                Go to Homepage
            </a>
            <button onclick="window.history.back()" class="w-full sm:w-auto px-8 py-4 bg-white border-2 border-gray-100 text-gray-600 hover:border-rose-500 hover:text-rose-500 rounded-2xl font-bold transition-all active:scale-95">
                Go Back
            </button>
        </div>

        <div class="mt-16 grid grid-cols-3 gap-8 grayscale opacity-40">
            <i class="bi bi-airplane text-3xl"></i>
            <i class="bi bi-map text-3xl"></i>
            <i class="bi bi-luggage text-3xl"></i>
        </div>
    </div>
</div>
@endsection