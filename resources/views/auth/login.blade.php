@extends('layouts.auth')

@section('content')
<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    <!-- LEFT: IMAGE + BRAND -->
    <div class="relative hidden lg:flex items-center">
        <div class="absolute inset-0">
            <img src="{{ asset('images/login-bg.jpg') }}" alt="B2B platform" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <div class="relative z-10 px-20 text-white">
            <h1 class="text-5xl font-bold mb-6">ACROVOY</h1>
            <p class="text-2xl leading-snug max-w-xl">
                Global B2B platform connecting manufacturers, suppliers and buyers.
            </p>
        </div>
    </div>

    <!-- RIGHT: LOGIN -->
    <div class="flex items-center justify-center bg-white">
        <div class="w-full max-w-md px-8">

            <!-- Mobile brand -->
            <div class="mb-8 text-center lg:hidden">
                <h1 class="text-4xl font-bold mb-2">ACROVOY</h1>
                <p class="text-gray-600">B2B platform for manufacturers and buyers</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <input type="email" name="email" placeholder="Email address" required autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500">

                <input type="password" name="password" placeholder="Password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-md text-lg focus:outline-none focus:border-blue-500">

                <button type="submit"
                    class="w-full py-3 text-lg font-semibold text-white bg-[#1877f2] rounded-md hover:bg-[#166fe5]">
                    Log In
                </button>

                <!-- OR separator with rounded lines -->
                <div class="flex items-center my-4">
                    <div class="flex-grow h-px bg-gray-300 rounded"></div>
                    <span class="mx-3 text-gray-500 font-medium">or</span>
                    <div class="flex-grow h-px bg-gray-300 rounded"></div>
                </div>

                <!-- Social login buttons -->
                <div class="space-y-3">
                    <!-- Google -->
                    <a href="{{ route('login.google') }}"
                        class="w-full flex items-center justify-center py-3 text-lg font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-100 transition">
                        <svg class="w-5 h-5 mr-3" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#4285F4"
                                d="M533.5 278.4c0-18.4-1.6-36.1-4.7-53.3H272v100.9h146.9c-6.3 33.7-25.1 62.4-53.7 81.7v67h86.9c50.7-46.6 80.4-115.7 80.4-196.3z" />
                            <path fill="#34A853"
                                d="M272 544.3c72.9 0 134.1-24.2 178.8-65.8l-86.9-67c-24.2 16.2-55.2 25.9-91.9 25.9-70.6 0-130.4-47.7-151.7-111.7H31.1v69.8C75.5 487.2 168.5 544.3 272 544.3z" />
                            <path fill="#FBBC05"
                                d="M120.3 323.5c-10.5-31.2-10.5-64.7 0-95.9v-69.8H31.1c-42.3 83.8-42.3 182.6 0 266.4l89.2-70.7z" />
                            <path fill="#EA4335"
                                d="M272 107.7c38.8 0 73.7 13.3 101.2 39.5l75.8-75.8C405.7 24.5 344.5 0 272 0 168.5 0 75.5 57.1 31.1 144.2l89.2 69.8c21.3-64 81.1-111.7 151.7-111.7z" />
                        </svg>
                        Continue with Google
                    </a>

                    <!-- LinkedIn -->
                    <a href="{{ route('login.linkedin') }}"
                        class="w-full flex items-center justify-center py-3 text-lg font-medium text-white bg-[#0077B5] rounded-md shadow-sm hover:bg-[#00669C] transition">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34 34">
                            <path fill="white"
                                d="M34,3.4v27.2c0,1.9-1.5,3.4-3.4,3.4H3.4C1.5,34,0,32.5,0,30.6V3.4C0,1.5,1.5,0,3.4,0h27.2C32.5,0,34,1.5,34,3.4z" />
                            <path fill="#0077B5"
                                d="M5,29h5V13H5V29z M7.5,11.1c1.6,0,2.6-1.1,2.6-2.5c0-1.4-1-2.5-2.5-2.5S5,7.3,5,8.7C5,9.9,5.9,11.1,7.5,11.1z M12,29h5v-8.3c0-2.1,0.8-3.6,2.8-3.6c2,0,2,1.7,2,3.5V29h5v-8.9c0-4.7-2.5-6.8-5.8-6.8c-2.7,0-3.9,1.5-4.6,2.5h0V13h-5C12,13,12,29,12,29z" />
                        </svg>
                        Continue with LinkedIn
                    </a>
                </div>

                <!-- Forgot password -->
                <div class="text-center mt-4">
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-[#1877f2] hover:underline">
                        Forgot password?
                    </a>
                </div>

                <!-- Create account -->
                <a href="{{ route('register') }}"
                    class="block w-full text-center py-3 text-lg font-semibold text-white bg-[#42b72a] rounded-md hover:bg-[#36a420] mt-6">
                    Create new account
                </a>

            </form>

        </div>
    </div>

</div>
@endsection
