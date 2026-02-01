@extends('layouts.app')

@section('title', 'Register - traveldotcom')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <i class="bi bi-airplane-engines-fill text-accent text-4xl mb-2"></i>
                <h2 class="text-2xl font-bold text-gray-800">Create Account</h2>
                <p class="text-gray-500">Join traveldotcom today</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-accent focus:border-accent @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-accent focus:border-accent @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-accent focus:border-accent @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-accent focus:border-accent">
                </div>

                <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white py-3 rounded-lg font-bold transition">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-accent hover:underline font-bold">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection