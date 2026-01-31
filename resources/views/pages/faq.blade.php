@extends('layouts.app')

@section('content')
<div class="bg-[#F7F3EA] py-12">
    <div class="max-w-4xl mx-auto px-4">

        <h1 class="text-3xl font-bold mb-8 text-center">
            Frequently Asked Questions
        </h1>

        <div class="space-y-4">

            <details class="bg-white rounded-lg shadow-sm p-5">
                <summary class="font-semibold cursor-pointer">
                    How does messaging work?
                </summary>
                <p class="text-gray-600 mt-2">
                    Buyers and sellers can communicate directly through the Message Center.
                </p>
            </details>

            <details class="bg-white rounded-lg shadow-sm p-5">
                <summary class="font-semibold cursor-pointer">
                    How do I become a seller?
                </summary>
                <p class="text-gray-600 mt-2">
                    Register an account and switch to Seller mode in your dashboard.
                </p>
            </details>

            <details class="bg-white rounded-lg shadow-sm p-5">
                <summary class="font-semibold cursor-pointer">
                    Is it free to use?
                </summary>
                <p class="text-gray-600 mt-2">
                    Yes, the platform is free to start. Premium plans are optional.
                </p>
            </details>

        </div>

    </div>
</div>
@endsection
