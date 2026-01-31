@extends('layouts.app')

@stack('scripts')

@section('content')
<div class="bg-[#F7F3EA] py-8">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 px-4 items-start">

        {{-- LEFT MENU --}}
        <aside class="w-full lg:w-1/4 bg-white border border-gray-200 rounded-xl shadow-sm p-4 self-start">

    @php
        use App\Models\OrderDispute;
        use App\Models\RfqOffer;

        $openDisputeCount = 0;
        $newOfferCount = 0;
        $acceptedOfferCount = 0;

        if(auth()->check()) {
            // BUYER
            if(auth()->user()->role === 'buyer') {
                $openDisputeCount = OrderDispute::whereHas('order', function ($q) {
                        $q->where('user_id', auth()->id());
                    })
                    ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
                    ->count();

                $newOfferCount = RfqOffer::whereHas('rfq', function ($q) {
                        $q->where('buyer_id', auth()->id())
                          ->where('status', 'active');
                    })
                    ->whereNull('buyer_viewed_at')
                    ->count();
            }

            // SELLER
            if(auth()->user()->role === 'manufacturer' && auth()->user()->supplier) {
                $openDisputeCount = OrderDispute::whereHas('order.items.product', function ($q) {
                        $q->where('supplier_id', auth()->user()->supplier->id);
                    })
                    ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
                    ->count();

                $acceptedOfferCount = RfqOffer::where('supplier_id', auth()->user()->supplier->id)
                    ->where('status', 'accepted')
                    ->count();
            }
        }
    @endphp

    {{-- Role Switcher --}}
    <div class="mb-6 flex gap-2">
        @if(auth()->user()->role === 'manufacturer')
            <div class="flex-1 py-2 rounded-md font-semibold text-center border-2 border-brown-500 text-black bg-white">
                Seller
            </div>
        @endif
        @if(auth()->user()->role === 'buyer')
            <div class="flex-1 py-2 rounded-md font-semibold text-center border-2 border-brown-500 text-black bg-white">
                Buyer
            </div>
        @endif
    </div>

    {{-- Menu --}}
    @if(auth()->user()->role === 'manufacturer')
        <ul class="space-y-2">

            <li>
                <a href="{{ route('supplier.rfqs.index') }}"
                   class="flex items-center justify-between px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    <span>Available RFQs</span>
                    @if($acceptedOfferCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-green-600 rounded-full">
                            {{ $acceptedOfferCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('manufacturer.products.create') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    + Add Product
                </a>
            </li>

            <li>
                <a href="{{ route('manufacturer.products.index') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    Product List
                </a>
            </li>

            <li>
                <a href="{{ route('manufacturer.orders') }}"
                   class="flex items-center justify-between px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    <span>Manage Orders</span>
                    @if($openDisputeCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                            {{ $openDisputeCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('manufacturer.messages') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    Message Center
                </a>
            </li>

            <li>
                <a href="{{ route('manufacturer.shipping-templates.index') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    Shipping Center
                </a>
            </li>

            <li>
                <a href="{{ route('manufacturer.company.profile') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    Company Profile
                </a>
            </li>

            <li>
                <a href="{{ route('manufacturer.premium-plans') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    Premium Seller Plans
                </a>
            </li>

        </ul>
    @else
        {{-- Buyer Menu --}}
        <ul class="space-y-2">

            <li>
                <a href="{{ route('buyer.rfqs.index') }}"
                   class="flex items-center justify-between px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    <span>My RFQs</span>
                    @if($newOfferCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-indigo-600 rounded-full">
                            {{ $newOfferCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('buyer.cart.index') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    Cart
                </a>
            </li>

            <li>
                <a href="{{ route('buyer.orders.index') }}"
                   class="flex items-center justify-between px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    <span>My Orders</span>
                    @if($openDisputeCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                            {{ $openDisputeCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('buyer.messages') }}"
                   class="block px-4 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition font-medium">
                    Message Center
                </a>
            </li>

        </ul>
    @endif

</aside>




        {{-- RIGHT CONTENT --}}
        <main class="w-full lg:w-3/4 bg-white shadow-sm rounded-lg p-6 min-h-[400px]">
            @yield('dashboard-content')
        </main>

    </div>
</div>

<style>
.menu-link {
    display: block;
    padding: 0.5rem 1rem;      /* компактный padding */
    border-radius: 0.5rem;
    font-family: 'Figtree', sans-serif;
    line-height: 1.2;          /* уменьшенная высота строки */
    font-size: 0.95rem;        /* чуть меньший шрифт для компактности */
    transition: background 0.2s;
}
.menu-link:hover {
    background: #e0e7ff;
}
</style>
@endsection


