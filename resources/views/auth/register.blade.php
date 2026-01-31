@extends('layouts.auth')

@section('content')
<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    <!-- LEFT: IMAGE + BRAND -->
    <div class="relative hidden lg:flex items-center">
        <div class="absolute inset-0">
            <img
                src="{{ asset('images/register-bg.jpg') }}"
                alt="B2B platform"
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <div class="relative z-10 px-20 text-white">
            <h1 class="text-5xl font-bold mb-6">ACROVOY</h1>
            <p class="text-2xl leading-snug max-w-xl">
                Join the fastest growing B2B platform connecting manufacturers, suppliers, and buyers worldwide.
            </p>
        </div>
    </div>

    <!-- RIGHT: REGISTRATION FORM -->
<div class="flex items-center justify-center bg-white min-h-screen">
    <div class="w-full max-w-md px-8">

        <!-- Mobile brand -->
        <div class="mb-8 text-center lg:hidden">
            <h1 class="text-4xl font-bold mb-2">ACROVOY</h1>
            <p class="text-gray-600">
                B2B platform for manufacturers and buyers
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <input
                type="text"
                name="name"
                placeholder="Full Name"
                required
                autofocus
                class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500"
            >

            <!-- Email -->
            <input
                type="email"
                name="email"
                placeholder="Email address"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500"
            >

            <!-- Password -->
            <input
                type="password"
                name="password"
                placeholder="Password"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500"
            >

            <!-- Confirm Password -->
            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm Password"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500"
            >

            <!-- Submit button -->
            <button
                type="submit"
                class="w-full py-3 text-lg font-semibold text-white bg-[#42b72a] rounded-md hover:bg-[#36a420]"
            >
                Register
            </button>

            <hr class="my-6">

            <!-- Link to login -->
            <a href="{{ route('login') }}"
               class="block w-full text-center py-3 text-lg font-semibold text-white bg-[#1877f2] rounded-md hover:bg-[#166fe5]">
                Already have an account? Log In
            </a>

        </form>

    </div>
</div>


</div>
@endsection
