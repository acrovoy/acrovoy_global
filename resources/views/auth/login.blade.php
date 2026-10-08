@extends('layouts.auth')

@section('content')

<div class="min-h-screen bg-[#F7F3EA] grid grid-cols-1 lg:grid-cols-2">


{{-- ============================================================
    LEFT : IMAGE + BRAND
============================================================= --}}

<div class="relative hidden lg:flex items-center min-h-screen">

    <div class="absolute inset-0">
        <img
            src="{{ asset('images/login-bg.jpg') }}"
            alt="Acrovoy B2B platform"
            class="w-full h-full object-cover"
        >

        <div class="absolute inset-0 bg-black/45"></div>
    </div>

    <div class="relative z-10 px-16 xl:px-20 text-white max-w-2xl">

        <div class="mb-8">

            <div class="text-sm font-medium tracking-[0.25em] uppercase text-white/70">
                Global B2B Marketplace
            </div>

            <h1 class="mt-3 text-5xl xl:text-6xl font-semibold tracking-tight">
                ACROVOY
            </h1>

        </div>

        <p class="text-2xl xl:text-3xl leading-snug font-medium max-w-xl">
            Connecting trusted manufacturers, suppliers and buyers worldwide.
        </p>

        <p class="mt-6 text-sm leading-relaxed text-white/70 max-w-lg">
            Discover hotel and restaurant furniture, décor, outdoor collections
            and trusted suppliers from around the world.
        </p>

    </div>

</div>


{{-- ============================================================
    RIGHT : LOGIN
============================================================= --}}

<div class="flex items-center justify-center min-h-screen px-5 py-10 lg:px-10">

    <div class="w-full max-w-md">

        {{-- ====================================================
            LOGIN CARD
        ===================================================== --}}

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 sm:p-9">

            {{-- Mobile brand --}}

            <div class="mb-8 text-center lg:hidden">

                <div class="text-xs font-medium tracking-[0.2em] uppercase text-gray-400">
                    Global B2B Marketplace
                </div>

                <h1 class="mt-2 text-4xl font-semibold tracking-tight text-gray-900">
                    ACROVOY
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    B2B platform for manufacturers, suppliers and buyers
                </p>

            </div>


            {{-- Desktop heading --}}

            <div class="hidden lg:block mb-8">

                <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                    Welcome back
                </h2>

                <p class="mt-1.5 text-sm text-gray-500">
                    Sign in to continue to your Acrovoy account.
                </p>

            </div>


            {{-- =================================================
                FORM
            ================================================== --}}

            <form
                method="POST"
                action="{{ route('login') }}"
                class="space-y-5"
            >

                @csrf


                {{-- TIMEZONE --}}

                <input
                    type="hidden"
                    name="timezone"
                    id="login-timezone"
                >

                <script>
                    document.getElementById('login-timezone').value =
                        Intl.DateTimeFormat().resolvedOptions().timeZone;
                </script>


                {{-- EMAIL --}}

                <div>

                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Email address
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        required
                        autofocus
                        autocomplete="email"
                        class="
                            mt-2
                            w-full
                            h-11
                            px-3.5
                            rounded-xl
                            border
                            border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            placeholder-gray-400
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                    >

                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- PASSWORD --}}

                <div>

                    <div class="flex items-center justify-between gap-3">

                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Password
                        </label>

                        <a
                            href="{{ route('password.request') }}"
                            class="text-xs font-medium text-gray-500 hover:text-gray-900 transition"
                        >
                            Forgot password?
                        </a>

                    </div>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                        class="
                            mt-2
                            w-full
                            h-11
                            px-3.5
                            rounded-xl
                            border
                            border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            placeholder-gray-400
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                    >

                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- LOGIN BUTTON --}}

                <button
                    type="submit"
                    class="
                        w-full
                        h-11
                        rounded-xl
                        bg-gray-900
                        text-white
                        text-sm
                        font-semibold
                        hover:bg-gray-800
                        transition
                        focus:outline-none
                        focus:ring-2
                        focus:ring-gray-900
                        focus:ring-offset-2
                    "
                >
                    Sign in
                </button>


                {{-- =================================================
                    OR
                ================================================== --}}

                <div class="flex items-center gap-4 py-1">

                    <div class="flex-1 h-px bg-gray-200"></div>

                    <span class="text-xs font-medium text-gray-400">
                        OR
                    </span>

                    <div class="flex-1 h-px bg-gray-200"></div>

                </div>


                {{-- =================================================
                    SOCIAL LOGIN
                ================================================== --}}

                <div class="space-y-3">

                    {{-- GOOGLE --}}

                    <a
                        href="{{ route('login.google') }}"
                        class="
                            w-full
                            h-11
                            flex
                            items-center
                            justify-center
                            gap-3
                            rounded-xl
                            border
                            border-gray-200
                            bg-white
                            text-sm
                            font-medium
                            text-gray-700
                            hover:bg-gray-50
                            hover:border-gray-300
                            transition
                        "
                    >

                        <svg
                            class="w-5 h-5 shrink-0"
                            viewBox="0 0 533.5 544.3"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill="#4285F4"
                                d="M533.5 278.4c0-18.4-1.6-36.1-4.7-53.3H272v100.9h146.9c-6.3 33.7-25.1 62.4-53.7 81.7v67h86.9c50.7-46.6 80.4-115.7 80.4-196.3z"
                            />
                            <path
                                fill="#34A853"
                                d="M272 544.3c72.9 0 134.1-24.2 178.8-65.8l-86.9-67c-24.2 16.2-55.2 25.9-91.9 25.9-70.6 0-130.4-47.7-151.7-111.7H31.1v69.8C75.5 487.2 168.5 544.3 272 544.3z"
                            />
                            <path
                                fill="#FBBC05"
                                d="M120.3 323.5c-10.5-31.2-10.5-64.7 0-95.9v-69.8H31.1c-42.3 83.8-42.3 182.6 0 266.4l89.2-70.7z"
                            />
                            <path
                                fill="#EA4335"
                                d="M272 107.7c38.8 0 73.7 13.3 101.2 39.5l75.8-75.8C405.7 24.5 344.5 0 272 0 168.5 0 75.5 57.1 31.1 144.2l89.2 69.8c21.3-64 81.1-111.7 151.7-111.7z"
                            />
                        </svg>

                        Continue with Google

                    </a>


                    {{-- LINKEDIN --}}

                    <a
                        href="{{ route('login.linkedin') }}"
                        class="
                            w-full
                            h-11
                            flex
                            items-center
                            justify-center
                            gap-3
                            rounded-xl
                            border
                            border-gray-200
                            bg-white
                            text-sm
                            font-medium
                            text-gray-700
                            hover:bg-gray-50
                            hover:border-gray-300
                            transition
                        "
                    >

                        <svg
                            class="w-5 h-5 shrink-0"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 34 34"
                        >
                            <path
                                fill="#0077B5"
                                d="M34,3.4v27.2c0,1.9-1.5,3.4-3.4,3.4H3.4C1.5,34,0,32.5,0,30.6V3.4C0,1.5,1.5,0,3.4,0h27.2C32.5,0,34,1.5,34,3.4z"
                            />

                            <path
                                fill="white"
                                d="M5,29h5V13H5V29z M7.5,11.1c1.6,0,2.6-1.1,2.6-2.5c0-1.4-1-2.5-2.5-2.5S5,7.3,5,8.7C5,9.9,5.9,11.1,7.5,11.1z M12,29h5v-8.3c0-2.1,0.8-3.6,2.8-3.6c2,0,2,1.7,2,3.5V29h5v-8.9c0-4.7-2.5-6.8-5.8-6.8c-2.7,0-3.9,1.5-4.6,2.5h0V13h-5C12,13,12,29,12,29z"
                            />
                        </svg>

                        Continue with LinkedIn

                    </a>

                </div>


                {{-- =================================================
                    CREATE ACCOUNT
                ================================================== --}}

                <div class="pt-5 mt-1 border-t border-gray-100">

                    <p class="text-center text-sm text-gray-500">
                        Don't have an account?
                    </p>

                    <a
                        href="{{ route('register') }}"
                        class="
                            mt-3
                            w-full
                            h-11
                            flex
                            items-center
                            justify-center
                            rounded-xl
                            border
                            border-gray-300
                            bg-white
                            text-sm
                            font-semibold
                            text-gray-900
                            hover:bg-gray-50
                            hover:border-gray-400
                            transition
                        "
                    >
                        Create an account
                    </a>

                </div>

            </form>

        </div>


        {{-- FOOTER --}}

        <p class="mt-6 text-center text-xs text-gray-400">
            © {{ date('Y') }} Acrovoy. All rights reserved.
        </p>

    </div>

</div>


</div>

@endsection
