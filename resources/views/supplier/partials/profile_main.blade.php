<div class="bg-gradient-to-br from-white via-[#fcfaf6] to-white border border-gray-200 shadow-xl shadow-gray-100 rounded-3xl p-12 space-y-14">

   


    {{-- ================= OVERVIEW ================= --}}
    <div class="space-y-6 max-w-5xl">

        <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-800 pl-4">
            Manufacturing Overview
        </h2>

        <div class="text-sm text-gray-600 leading-relaxed">
            {!! $supplier->description ?? 'No company profile available.' !!}
        </div>
    </div>

<div class="space-y-6">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-emerald-600 pl-4">
        Performance Intelligence
    </h2>

    <div class="grid md:grid-cols-4 gap-4">

        {{-- Rating --}}
        <div class="p-4 border rounded-xl bg-white shadow-sm hover:shadow transition">

            <div class="text-[11px] uppercase tracking-wider text-gray-400 mb-1">
                Rating
            </div>

            <div class="text-base font-bold text-amber-500 leading-tight">
                {{ $supplierRating }}/5
            </div>

            <div class="text-[11px] text-gray-500 mt-1">
                {{ $reviewsProductsCount }} reviews
            </div>

        </div>


        {{-- Response Time --}}
        <div class="p-4 border rounded-xl bg-white shadow-sm hover:shadow transition">

            <div class="text-[11px] uppercase tracking-wider text-gray-400 mb-1">
                Response Time
            </div>

            <div class="text-base font-semibold text-gray-900">
                ≤ {{ $supplier->response_time ?? '2' }}h
            </div>

        </div>


        {{-- Delivery Rate --}}
        <div class="p-4 border rounded-xl bg-white shadow-sm hover:shadow transition">

            <div class="text-[11px] uppercase tracking-wider text-gray-400 mb-1">
                On-time Delivery
            </div>

            <div class="text-base font-semibold text-green-600">
                {{ $supplier->on_time_delivery_rate ?? '75' }}%
            </div>

        </div>


        {{-- Orders --}}
        <div class="p-4 border rounded-xl bg-white shadow-sm hover:shadow transition">

            <div class="text-[11px] uppercase tracking-wider text-gray-400 mb-1">
                Completed Orders
            </div>

            <div class="text-base font-semibold text-gray-900">
                28
            </div>

        </div>

         {{-- Orders --}}
        <div class="p-4 border rounded-xl bg-white shadow-sm hover:shadow transition">

            <div class="text-[11px] uppercase tracking-wider text-gray-400 mb-1">
                Completed Orders
            </div>

            <div class="text-base font-semibold text-gray-900">
                28
            </div>

        </div>

    </div>

</div>



{{-- ================= CAPABILITY SIGNALS ================= --}}
<div class="space-y-6">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-800 pl-4">
        Manufacturing Capability
    </h2>

    <div class="grid md:grid-cols-2 gap-4 text-sm">

        @php
        $capabilities = [
            'Custom production available' => $supplier->custom_production ?? false,
            'Sample manufacturing support' => $supplier->sample_production ?? false,
            'Raw material traceability' => $supplier->traceability ?? false,
            'Quality control on production line' => $supplier->quality_control ?? false,
        ];
        @endphp

        @foreach($capabilities as $name => $value)
        <div class="flex items-center gap-3 p-4 border rounded-xl bg-white shadow-sm">
            <span class="text-emerald-600">
                @if($value)
                ✔
                @else
                ✖
                @endif
            </span>

            <span class="{{ $value ? 'text-gray-800' : 'text-gray-400' }}">
                {{ $name }}
            </span>
        </div>
        @endforeach

    </div>
</div>



{{-- ================= CERTIFICATION PREVIEW ================= --}}
<div class="space-y-6">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-800 pl-4">
        Certifications
    </h2>

    <div class="flex flex-wrap gap-4">

        @forelse($supplier->certificates ?? [] as $cert)
        <div class="w-32 border rounded-xl p-3 text-center bg-white shadow-sm hover:shadow-md transition">

            <div class="h-20 flex items-center justify-center text-xs text-gray-500">
                {{ strtoupper(pathinfo($cert->file_path, PATHINFO_EXTENSION)) }}
            </div>

            <div class="text-xs truncate mt-2">
                {{ $cert->name }}
            </div>

        </div>
        @empty
        <div class="text-sm text-gray-400">
            No certifications uploaded
        </div>
        @endforelse

    </div>
</div>


    {{-- ================= INDUSTRIAL CAPABILITY ================= --}}
    <div class="space-y-8">

        <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-800 pl-4">
            Production Capability
        </h2>


        <div class="grid md:grid-cols-3 gap-8">

            



    {{-- Certification Count --}}
    <div class="p-7 border border-gray-200 rounded-2xl bg-white shadow-sm hover:shadow-md transition">
        <div class="text-xs uppercase tracking-wider text-gray-400 mb-2">
            Certification Count
        </div>

        <div class="text-xl font-bold text-gray-900">
            {{ $supplier->certificates->count() }}
        </div>
    </div>



    {{-- Registration Date --}}
    <div class="p-7 border border-gray-200 rounded-2xl bg-white shadow-sm hover:shadow-md transition">
        <div class="text-xs uppercase tracking-wider text-gray-400 mb-2">
            Company Registration Date
        </div>

        <div class="text-xl font-bold text-gray-900">
            {{ optional($supplier->created_at)->format('Y-m-d') }}
        </div>
    </div>



    {{-- Factory Area --}}
    <div class="p-7 border border-gray-200 rounded-2xl bg-white shadow-sm hover:shadow-md transition">
        <div class="text-xs uppercase tracking-wider text-gray-400 mb-2">
            Production Area (sq.m)
        </div>

        <div class="text-xl font-bold text-gray-900">
            2580 м2
        </div>
    </div>

        </div>
    </div>


    {{-- ================= FACTORY SECTION ================= --}}
    <div class="space-y-8">

        <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-800 pl-4">
            Factory & Production Base
        </h2>


        <div class="grid md:grid-cols-4 gap-6">

            @for($i = 0; $i < 4; $i++)
                <div class="aspect-square rounded-xl border border-gray-200 bg-gray-100 hover:shadow-lg transition duration-300"></div>
            @endfor

        </div>
    </div>


    {{-- ================= TIMELINE ================= --}}
<div class="space-y-6">

    <h2 class="text-xl font-semibold text-gray-900 border-l-4 border-yellow-800 pl-4">
        Company Development Journey
    </h2>


    <div class="relative border-l-2 border-gray-200 ml-3 pl-6 space-y-6">

        {{-- Founded --}}
        <div class="relative group">

            <div class="absolute -left-[13px] top-1 w-5 h-5 rounded-full
                    bg-yellow-500 ring-4 ring-yellow-100"></div>

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 max-w-md">

                <div class="text-xs uppercase tracking-wider text-gray-400 mb-1">
                    {{ $supplier->founded_year ?? '2015' }}
                </div>

                <div class="font-semibold text-gray-900 text-sm">
                    Company Established
                </div>

                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Market entry and foundation stage.
                </p>

            </div>

        </div>


        {{-- Expansion --}}
        <div class="relative group">

            <div class="absolute -left-[13px] top-1 w-5 h-5 rounded-full
                    bg-emerald-600 ring-4 ring-emerald-100"></div>

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 max-w-md">

                <div class="text-xs uppercase tracking-wider text-gray-400 mb-1">
                    {{ now()->year - 2 }}
                </div>

                <div class="font-semibold text-gray-900 text-sm">
                    Production Expansion Phase
                </div>

                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    Manufacturing capacity scaling and global operations.
                </p>

            </div>

        </div>

    </div>

</div>








</div>