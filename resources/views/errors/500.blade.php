@extends('layouts.app')

@section('title', 'Something Went Wrong - traveldotcom')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center max-w-lg w-full">
        <div class="relative mb-8">
            <h1 class="text-9xl font-black text-gray-100 uppercase tracking-tighter">500</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-rose-500 text-6xl animate-bounce"></i>
            </div>
        </div>

        <h2 class="text-3xl font-bold text-gray-900 mb-4">Turbulence detected</h2>
        <p class="text-gray-500 mb-10 leading-relaxed">
            Something went wrong on our end. We're already working to fix the issue and get things back to normal. Please try again later.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl font-bold transition-all shadow-lg active:scale-95">
                Try Again
            </button>
            <a href="{{ route('main.index') }}" class="w-full sm:w-auto px-8 py-4 bg-white border-2 border-gray-100 text-gray-600 hover:border-rose-500 hover:text-rose-500 rounded-2xl font-bold transition-all active:scale-95">
                Back Home
            </a>
        </div>
    </div>
</div>
@endsection