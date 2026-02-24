@extends('layouts.app')

@section('content')
<section class="bg-[#F7F3EA] py-8">
    <div class="container mx-auto px-6">
                              
        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-6">
            <a href="/suppliers" class="hover:text-black">Suppliers</a> /
            <span class="text-gray-900">{{ $supplier->name }}</span>
        </div>
        
        {{-- Hero --}}
        @include('supplier.sections.hero')

        <x-supplier.tabs :tabs="$tabs" :supplier="$supplier"/>

        @if($activeTab === 'home')
            @include('supplier.partials.home')
        @endif

        @if($activeTab === 'profile')
            @include('supplier.partials.profile')
        @endif

        @if($activeTab === 'products')
            @include('supplier.partials.products')
        @endif

        @if($activeTab === 'contacts')
            @include('supplier.partials.contacts')
        @endif
        
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
        inset 0 2px 6px rgba(0,0,0,0.04),
        0 -2px 4px rgba(0,0,0,0.03);
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

    background: linear-gradient(
        to bottom,
        rgba(255,255,255,0.6),
        rgba(0,0,0,0.03)
    );

    border-radius: 999px;
    
}







</style>
@endsection
