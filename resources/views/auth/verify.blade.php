@extends('layouts.app')

@section('title', 'Verify Email - Travel.com')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-envelope-exclamation text-yellow-600 text-4xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Verify Your Email</h2>
                <p class="text-gray-500 mt-2">We've sent a verification link to your email address.</p>
            </div>

            @if (session('resent'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                <i class="bi bi-check-circle me-2"></i>A fresh verification link has been sent to your email address.
            </div>
            @endif

            <p class="text-gray-600 mb-6">
                Before proceeding, please check your email for a verification link.
                If you didn't receive the email, click below to request another.
            </p>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white py-3 rounded-lg font-bold transition">
                    <i class="bi bi-envelope me-2"></i>Resend Verification Email
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Wrong email?
                    <a href="{{ route('home.edit') }}" class="text-accent hover:underline">Update your email address</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection