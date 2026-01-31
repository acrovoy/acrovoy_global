@extends('layouts.auth')

@section('content')
<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    <!-- LEFT: IMAGE + INFO -->
    <div class="relative hidden lg:flex items-center">
        <div class="absolute inset-0">
            <img
                src="{{ asset('images/login-bg.jpg') }}"
                alt="Manufacturer registration"
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-black/60"></div>
        </div>

        <div class="relative z-10 px-20 text-white">
            <h1 class="text-5xl font-bold mb-8">ACROVOY</h1>

            <div class="space-y-6 max-w-xl">
                <p class="text-2xl leading-snug">
                    ACROVOY is a professional B2B platform built for manufacturers
                    and suppliers ready to scale their global sales.
                </p>

                <ul class="space-y-4 text-lg">
                    <li class="flex gap-3">
                        <span class="text-green-400 font-bold">✓</span>
                        Create a verified manufacturer profile and present your
                        products to international buyers.
                    </li>

                    <li class="flex gap-3">
                        <span class="text-green-400 font-bold">✓</span>
                        Receive direct inquiries from buyers — no intermediaries,
                        no hidden commissions.
                    </li>

                    <li class="flex gap-3">
                        <span class="text-green-400 font-bold">✓</span>
                        Manage product listings, specifications, pricing and
                        availability from one dashboard.
                    </li>

                    <li class="flex gap-3">
                        <span class="text-green-400 font-bold">✓</span>
                        Expand into new markets with multi-currency and
                        country-based product visibility.
                    </li>

                    <li class="flex gap-3">
                        <span class="text-green-400 font-bold">✓</span>
                        Build long-term partnerships with verified B2B buyers worldwide.
                    </li>
                </ul>

                <p class="text-xl font-semibold pt-4">
                    Join ACROVOY and turn your manufacturing capacity into global demand.
                </p>
            </div>
        </div>
    </div>

    <!-- RIGHT: REGISTRATION FORM -->
    <div class="flex items-center justify-center bg-white min-h-screen">
        <div class="w-full max-w-md px-8">

            <!-- Mobile brand -->
            <div class="mb-8 text-center lg:hidden">
                <h1 class="text-4xl font-bold mb-2">ACROVOY</h1>
                <p class="text-gray-600 leading-relaxed">
                    Sell your products globally, receive direct B2B inquiries,
                    and manage your manufacturer profile in one professional platform.
                </p>
            </div>

            <!-- Validation errors -->
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-600">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM (НЕ ИЗМЕНЕНА) -->
            <form method="POST" action="{{ route('register.manufacturer') }}" class="space-y-4">
                @csrf

                <!-- Company / Name -->
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Company name or Full name"
                    required
                    autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500"
                >

                <!-- Business Email -->
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Business email address"
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
                    placeholder="Confirm password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500"
                >

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full py-3 text-lg font-semibold text-white bg-[#42b72a] rounded-md hover:bg-[#36a420] transition"
                >
                    Register as Manufacturer
                </button>

                <hr class="my-6">

                <!-- Link to buyer registration -->
                <a href="{{ route('register') }}"
                   class="block w-full text-center py-3 text-lg font-semibold text-white bg-gray-800 rounded-md hover:bg-gray-700 transition">
                    Register as Buyer
                </a>

                <!-- Link to login -->
                <a href="{{ route('login') }}"
                   class="block w-full text-center py-3 text-lg font-semibold text-white bg-[#1877f2] rounded-md hover:bg-[#166fe5] transition">
                    Already have an account? Log In
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
