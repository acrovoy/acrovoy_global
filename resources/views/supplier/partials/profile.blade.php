
<div class="grid lg:grid-cols-3 gap-4">

    {{-- ================= LEFT PROFILE CONTENT ================= --}}
    <div class="lg:col-span-2 space-y-12">

        {{-- 👉 Твой существующий профильный контент вставь сюда --}}
        @include('supplier.partials.profile_main')

    </div>



    {{-- ================= RIGHT SIDEBAR ================= --}}
<div class="space-y-6">

    <div class="sticky top-24 space-y-6">

        {{-- ================= SUPPLIER SNAPSHOT ================= --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-6">

            <h3 class="font-semibold text-lg text-gray-900 border-l-4 border-yellow-800 pl-3">
                Supplier Snapshot
            </h3>

            {{-- Rating --}}
            <div class="flex items-center justify-between pt-2 border-t">
                <div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $supplierRating ?: '—' }}<span class="text-sm font-medium text-gray-500">/5</span>
                    </div>
                    <div class="text-xs text-gray-500">
                        Based on {{ $count }} verified reviews
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2">


                {{-- SUPPLIER LEVEL --}}
    

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full 
    text-[9px] font-semibold tracking-wide whitespace-nowrap
    transition-all duration-200 hover:shadow-md
    {{ $level === 'Basic' ? 'bg-gray-50 text-gray-400 border border-gray-200' : '' }}
    {{ $level === 'Silver' ? 'bg-gray-200 text-gray-700 border border-gray-300' : '' }}
    {{ $level === 'Gold' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
    {{ $level === 'Platinum' ? 'bg-slate-800 text-white border border-slate-700 shadow-sm' : '' }}
">

    {{-- ICON --}}
    @if($level === 'Basic')
        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8" />
        </svg>

    @elseif($level === 'Silver')
        <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M12 3l7 18H5l7-18z" />
        </svg>

    @elseif($level === 'Gold')
        <svg class="w-3.5 h-3.5 opacity-90" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M5 12l5 5L20 7" />
        </svg>

    @elseif($level === 'Platinum')
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M12 2l3 7h7l-5.5 4.2L18 21l-6-4-6 4 1.5-7.8L2 9h7z" />
        </svg>
    @endif

    <span class="uppercase">
        {{ $level }} Supplier
    </span>

</span>


    

     {{-- PREMIUM STATUS --}}
    @if($supplier->is_premium)
    <span class="text-[9px] text-white font-medium px-3 py-1 rounded-full 
                 bg-gradient-to-br from-blue-700 via-blue-900 to-blue-500 border border-blue-200 text-blue-800 uppercase">
        Premium Supplier
    </span>
    @endif

    
     {{-- VERIFIED STATUS --}}
    @if($supplier->is_verified)
    <span class="text-[9px] text-gray-500 font-medium px-3 py-1 rounded-full 
                 bg-blue-100 border border-blue-200 uppercase">
        Verified Supplier
    </span>
    @endif

    {{-- TRUST STATUS --}}
    @if($supplier->is_trusted)
    <span class="text-[9px] text-gray-500 font-medium px-3 py-1 rounded-full 
                 bg-green-50 border border-green-200 text-green-700 uppercase">
        Trusted Supplier
    </span>
    @endif
    
    

</div>
            </div>



            {{-- Core Metrics --}}
            <div class="grid grid-cols-2 gap-4 text-sm pt-4 border-t">

                <div>
                    <div class="text-gray-400 text-xs">Response Time</div>
                    <div class="font-semibold text-gray-900">≤4h</div>
                </div>

                <div>
                    <div class="text-gray-400 text-xs">On-Time Delivery</div>
                    <div class="font-semibold text-gray-900">96.8%</div>
                </div>

                <div>
                    <div class="text-gray-400 text-xs">Orders Completed</div>
                    <div class="font-semibold text-gray-900">28</div>
                </div>

                <div>
                    <div class="text-gray-400 text-xs">Customization</div>
                    <div class="font-semibold text-gray-900">Available</div>
                </div>

            </div>



            {{-- Capabilities --}}
            <div class="pt-4 border-t space-y-3">

                <div class="text-xs uppercase tracking-wide text-gray-400">
                    Manufacturing Capabilities
                </div>

                <div class="flex flex-wrap gap-2 text-xs">

                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                        Drawing-based customization
                    </span>

                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                        Minor customization
                    </span>

                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                        Raw material traceability
                    </span>

                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                        Finished product inspection
                    </span>

                </div>
            </div>

        </div>



        {{-- ================= CONTACT BLOCK ================= --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-5">

            <h3 class="font-semibold text-lg text-gray-900 border-l-4 border-yellow-800 pl-3">
                Contact Supplier
            </h3>

            <button class="w-full bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold py-3 rounded-xl transition">
                Send Inquiry
            </button>

            <button class="w-full py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                Chat Now
            </button>

            

        </div>

    </div>

</div>







</div>









