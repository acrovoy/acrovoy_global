@extends('layouts.app')
@section('content')
@php
$company = $supplier;
@endphp
@php
$level = $company->level;

$glow = match($level) {
'Basic' => '#d1d5db',
'Silver' => '#cbd5e1',
'Gold' => '#fbbf24',
'Platinum' => '#475569',
default => '#d1d5db',
};
@endphp







<section class="bg-[#F7F3EA] py-8">
    <div class="container mx-auto px-6">

        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-6">
            <a href="/suppliers" class="hover:text-black">Suppliers</a> /
            <span class="text-gray-900">{{ $supplier->name }}</span>
        </div>







        <div class="mt-8 flex flex-col lg:flex-row gap-8">



            <aside class="w-full lg:w-72 shrink-0">

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden sticky top-6">

                    {{-- HEADER --}}
                    <div class="relative overflow-hidden px-6 py-7 border-b border-gray-200 bg-white">

                        <div class="absolute inset-0 pointer-events-none overflow-hidden">

                            <div
                                class="absolute -top-20 -right-16 w-52 h-52 rounded-full blur-3xl opacity-20"
                                style="background: {{ $glow }};">
                            </div>

                            <div
                                class="absolute -bottom-20 -left-16 w-56 h-56 rounded-full blur-3xl opacity-10"
                                style="background: {{ $glow }};">
                            </div>

                        </div>


                        <div class="flex flex-col items-center text-center">

                            <div class="w-20 h-20 rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">
                                <img
                                    src="{{ $company->logo?->cdn_url ?? asset('images/no-logo.png') }}"
                                    class="w-full h-full object-cover">
                            </div>

                            <h3 class="mt-4 font-semibold text-lg text-gray-900">
                                {{ $company->name }}
                            </h3>

                            <div class="mt-2">

                                @php
                                $level = $company->level;
                                @endphp

                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold

                        {{ $level === 'Basic'
                            ? 'bg-gray-100 text-gray-600 border border-gray-200'
                            : '' }}

                        {{ $level === 'Silver'
                            ? 'bg-gray-200 text-gray-700 border border-gray-300'
                            : '' }}

                        {{ $level === 'Gold'
                            ? 'bg-amber-100 text-amber-700 border border-amber-200'
                            : '' }}

                        {{ $level === 'Platinum'
                            ? 'bg-slate-900 text-white border border-slate-700'
                            : '' }}
                    ">

                                    {{ strtoupper($level) }} SUPPLIER

                                </span>

                            </div>

                        </div>

                    </div>



                    {{-- MENU --}}
                    <div class="p-4">

                        <div class="text-[11px] uppercase tracking-[0.18em] text-gray-400 font-semibold mb-3">
    {{ $is_personal ? 'Business Profile' : 'Company' }}
</div>

                        @php
                        $currentTab = request('tab', 'profile');
                        @endphp

                        <nav class="space-y-1">

                            @foreach($tabs as $id => $label)

                            <a href="{{ request()->fullUrlWithQuery(['tab' => $id]) }}"
                                class="flex items-center rounded-xl px-4 py-3 transition

       {{ $currentTab == $id
            ? 'bg-[#f7f3ec] border border-[#e8ddd0] text-[#6f4e37] font-semibold'
            : 'text-gray-700 hover:bg-gray-50'
       }}">

                                {{ $label }}

                            </a>

                            @endforeach

                        </nav>

                    </div>



                    




                    {{-- CHAT --}}
                    <div class="p-4 border-t border-gray-200">

                        <div class="text-[11px] uppercase tracking-[0.18em] text-gray-400 font-semibold mb-3">
                            Communication
                        </div>

                        <button
                            class="open-conversation
        inline-flex
        items-center
        gap-2
        px-3
        py-1.5
        rounded-lg
        border
        border-stone-200
        bg-white
        text-stone-600
        text-xs
        font-medium
        hover:border-stone-300
        hover:bg-stone-50
        hover:text-stone-900
        transition
        shadow-sm">

                            <svg class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 10h8M8 14h5M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.2-3.2A7.7 7.7 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>

                            Chat with Buyer

                        </button>

                    </div>


                 

                   

                </div>

            </aside>
            <div class="flex-1 min-w-0">
               

                @if($activeTab === 'profile')
                @include('buyer.partials.profile')
                @endif

                
                @if($activeTab === 'contacts')
                @include('buyer.partials.contacts')
                @endif
            </div>
        </div>

</section>

<style>
    .paper-notch {
        position: relative;
        overflow: hidden;
    }

    /* Main notch cut */
    .paper-notch::after {
        content: "";
        position: absolute;
        bottom: -22px;
        left: 50%;
        transform: translateX(-50%);

        width: 150px;
        height: 48px;

        background: #0c7448;

        border-radius: 999px;

        box-shadow:
            inset 0 2px 6px rgba(0, 0, 0, 0.04),
            0 -2px 4px rgba(0, 0, 0, 0.03);
    }

    /* Slight paper depth highlight */
    .paper-notch::before {
        content: "";
        position: absolute;
        bottom: -26px;
        left: 50%;
        transform: translateX(-50%);

        width: 160px;
        height: 56px;

        background: linear-gradient(to bottom,
                rgba(255, 255, 255, 0.6),
                rgba(0, 0, 0, 0.03));

        border-radius: 999px;

    }
</style>
@endsection