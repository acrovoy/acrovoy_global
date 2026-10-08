@extends('layouts.auth')

@section('content')

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-[#F7F3EA]">


<!-- LEFT: IMAGE + BRAND -->
<div class="relative hidden lg:flex items-center min-h-screen">

    <!-- Background image -->
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/login-bg.jpg') }}"
            alt="B2B platform"
            class="w-full h-full object-cover"
        >

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 px-16 xl:px-20 text-white">

        <h1 class="text-5xl font-bold tracking-tight mb-6">
            ACROVOY
        </h1>

        <p class="text-2xl leading-snug max-w-xl text-white/95">
            Global B2B platform connecting manufacturers,
            suppliers and buyers.
        </p>

    </div>

</div>

<!-- RIGHT: FORGOT PASSWORD -->
<div class="flex items-center justify-center bg-[#F7F3EA] min-h-screen px-4 py-8 sm:px-6">

    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 sm:p-9">

            <!-- Mobile brand -->
            <div class="mb-8 text-center lg:hidden">

                <div class="text-3xl font-bold tracking-tight text-gray-900">
                    ACROVOY
                </div>

                <p class="mt-2 text-sm text-gray-500">
                    B2B platform for manufacturers and buyers
                </p>

            </div>

            <!-- Heading -->
            <div class="mb-7">

                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Forgot your password?
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-gray-500">
                    Enter the email address associated with your account
                    and we'll send you a link to reset your password.
                </p>

            </div>

            <form
                method="POST"
                action="{{ route('password.email') }}"
                class="space-y-4"
            >
                @csrf

                <!-- Email Address -->
                <div>

                    <label
                        for="email"
                        class="block mb-1.5 text-sm font-medium text-gray-700"
                    >
                        Email address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="Email address"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full h-11 px-4 text-sm text-gray-900
                               bg-gray-50 border border-gray-200 rounded-xl
                               placeholder:text-gray-400
                               focus:outline-none
                               focus:ring-2 focus:ring-gray-900/10
                               focus:border-gray-400
                               transition"
                    >

                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full h-11 text-sm font-semibold text-white
                           bg-gray-900 rounded-xl
                           hover:bg-gray-800
                           focus:outline-none
                           focus:ring-2 focus:ring-gray-900/20
                           transition"
                >
                    Send Password Reset Link
                </button>

                <!-- Divider -->
                <div class="flex items-center gap-3 my-6">

                    <div class="flex-1 h-px bg-gray-200"></div>

                    <span class="text-xs font-medium text-gray-400">
                        OR
                    </span>

                    <div class="flex-1 h-px bg-gray-200"></div>

                </div>

                <!-- Back to Login -->
                <a
                    href="{{ route('login') }}"
                    class="flex items-center justify-center w-full h-11
                           text-sm font-semibold text-gray-700
                           bg-white border border-gray-200 rounded-xl
                           hover:bg-gray-50 hover:border-gray-300
                           transition"
                >
                    Back to Login
                </a>

            </form>

        </div>

        <!-- Footer -->
        <p class="mt-5 text-center text-xs text-gray-400">
            © {{ date('Y') }} Acrovoy. All rights reserved.
        </p>

    </div>

</div>


</div>

@endsection
