@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-7xl mx-auto space-y-4">

{{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Company Profile</h2>
            <p class="text-sm text-gray-500">
                Manage manufacturer identity and marketplace listing settings
            </p>
        </div>

        <div class="flex items-center gap-3">
            

           
        </div>
    </div>




    {{-- ================= CERTIFICATES BLOCK ================= --}}

<div class="bg-white border rounded-2xl shadow-sm p-6 space-y-5">

<div class="flex justify-between">
    <h3 class="text-sm text-gray-400 uppercase tracking-wider">
        Certificates
    </h3>
    <button
        type="button"
        onclick="openModal('certificatesModal')"
        class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 transition">

        + Certificates
    </button>
</div>
    @if($company->certificates->count())

    <div class="flex flex-wrap gap-4">

        @foreach($company->certificates as $certificate)

            <x-supplier.certificate-card :certificate="$certificate" />

        @endforeach

    </div>

    @else

    <p class="text-sm text-gray-400">
        No certificates uploaded yet
    </p>

    @endif

</div>

{{-- ================= CERTIFICATE MODAL ================= --}}

<div id="certificatesModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden">

        <div class="flex justify-between items-center px-6 py-4 border-b">

            <h3 class="text-lg font-semibold">
                Manage certificates
            </h3>

            <button type="button"
                    onclick="closeModal('certificatesModal')"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">

                &times;

            </button>

        </div>


        <div class="p-6 max-h-[70vh] overflow-y-auto">
            @include('dashboard.manufacturer.partials.manage-certificates')
        </div>

    </div>
</div>





{{-- ================= COMPANY PROFILE (COMPACT SINGLE CARD) ================= --}}
<div class="bg-white border rounded-2xl shadow-sm p-8 space-y-12">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-400 uppercase tracking-wider">
            Company Profile Overview
        </div>

        <a href="{{ route('manufacturer.company.profile') }}"
           class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 transition">
            Edit Profile
        </a>
    </div>


    {{-- ================= TOP SECTION ================= --}}
    <div class="grid lg:grid-cols-3 gap-10">

        {{-- Logo --}}
        <div>
            <div class="text-xs text-gray-400 uppercase mb-2">Logo</div>
            <div class="w-28 h-28 rounded-xl overflow-hidden border bg-gray-50">
                <img src="{{ $company->logo ? asset('storage/'.$company->logo) : asset('images/no-logo.png') }}"
                     class="w-full h-full object-cover">
            </div>
        </div>

        {{-- Core Info --}}
        <div class="lg:col-span-2 grid md:grid-cols-2 gap-x-10 gap-y-6">

            <div>
                <div class="text-xs text-gray-400 uppercase">Company Name</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->name }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Registration Country</div>
                <div class="text-gray-900">
                    {{ $company->country?->name ?? '—' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs text-gray-400 uppercase">Short Description</div>
                <div class="text-gray-900">
                    {{ $company->short_description ?? '—' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs text-gray-400 uppercase">Company Description</div>
                <div class="text-gray-700 leading-relaxed text-sm">
                    {!! $company->description ?? '—' !!}
                </div>
            </div>

        </div>
    </div>


    {{-- ================= GENERAL INFO ================= --}}
    <div class="grid lg:grid-cols-2 gap-12">

        <div class="space-y-6">

            <div>
                <div class="text-xs text-gray-400 uppercase mb-2">About Us</div>
                <div class="text-gray-700 leading-relaxed text-sm">
                    {!! $company->about_us_description ?? '—' !!}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase mb-3">
                    Export Markets
                </div>

                <div class="flex flex-wrap gap-2">
                    @forelse($company->exportMarkets as $market)
                        <span class="px-3 py-1 text-xs bg-gray-100 border rounded-full">
                            {{ $market->translation?->name ?? $market->slug }}
                        </span>
                    @empty
                        <span class="text-gray-400 text-sm">—</span>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Metrics --}}
        <div class="grid grid-cols-2 gap-6">

            <div>
                <div class="text-xs text-gray-400 uppercase">Founded</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->founded_year ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Employees</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->total_employees ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Export Revenue</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->annual_export_revenue ? '$'.number_format($company->annual_export_revenue) : '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Registration Capital</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->registration_capital ? '$'.number_format($company->registration_capital) : '—' }}
                </div>
            </div>

        </div>
    </div>


    {{-- ================= MANUFACTURING ================= --}}
    <div class="space-y-6">

        <div>
            <div class="text-xs text-gray-400 uppercase mb-2">
                Manufacturing Overview
            </div>
            <div class="text-gray-700 leading-relaxed text-sm">
                {!! $company->manufacturing_description ?? '—' !!}
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

            <div>
                <div class="text-xs text-gray-400 uppercase">Factory Area</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->factory_area ? $company->factory_area.' m²' : '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Lines</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->production_lines ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">MOQ</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->moq ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Capacity</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->monthly_capacity ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Lead Time</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->lead_time_days ? $company->lead_time_days.' days' : '—' }}
                </div>
            </div>

        </div>

    </div>


    {{-- ================= CONTACT ================= --}}
    <div class="grid md:grid-cols-3 gap-8">

        <div>
            <div class="text-xs text-gray-400 uppercase">Email</div>
            <div class="font-semibold text-gray-900">
                {{ $company->email ?? '—' }}
            </div>
        </div>

        <div>
            <div class="text-xs text-gray-400 uppercase">Phone</div>
            <div class="font-semibold text-gray-900">
                {{ $company->phone ?? '—' }}
            </div>
        </div>

        <div>
            <div class="text-xs text-gray-400 uppercase">Address</div>
            <div class="text-gray-900 text-sm">
                {{ $company->address ?? '—' }}
            </div>
        </div>

    </div>


    {{-- ================= CATALOG ================= --}}
    <div class="pt-6 border-t">

        <div class="text-xs text-gray-400 uppercase mb-6">
            Catalog Presentation
        </div>

        <div class="flex justify-center">
            @include('dashboard.manufacturer.partials.preview-card', [
                'company' => $company
            ])
        </div>

    </div>

</div>

</div>



<script>
function openModal(id){
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id){
    document.getElementById(id)?.classList.add('hidden');
}
</script>



@endsection