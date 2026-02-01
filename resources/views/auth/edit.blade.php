@extends('layouts.app')

@section('title', 'Edit Profile - traveldotcom')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="flex items-center mb-6">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 me-4">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Edit Profile</h2>
            </div>

            <form method="POST" action="{{ route('home.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="bi bi-info-circle me-1"></i>If you change your email, you'll need to verify it again.
                        </p>
                    </div>

                    <hr class="border-gray-200">

                    <h3 class="text-lg font-semibold text-gray-700">Change Password (optional)</h3>

                    {{-- Current Password --}}
                    <div>
                        <label for="current-password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="current-password" name="current-password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current-password') border-red-500 @enderror">
                        @error('current-password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="mt-8 flex space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                        <i class="bi bi-check-circle me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('home') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection