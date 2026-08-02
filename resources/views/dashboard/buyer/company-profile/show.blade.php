@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-7xl mx-auto space-y-4">

@php
            $level = $company->level;
            
            @endphp

{{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                @if($is_personal) Business Profile 
                @else Company Profile 
                @endif
            </h2>
            <p class="text-sm text-gray-500">
    @if($is_personal)
        Manage your supplier profile and professional marketplace presence
    @else
        Manage company identity, capabilities and marketplace visibility
    @endif
</p>
        </div>

        
<div class="flex items-center gap-3">

    @if(!$company->is_published)

        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M5 13l4 4L19 7"/>
            </svg>

            Publish Profile

        </button>

    @elseif($company->status === 'active')

        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">

            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M18 12H6"/>
            </svg>

            Unpublish

        </button>

    @else

        <div
            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-gray-50 px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm">

            <svg class="w-4 h-4 animate-spin"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 4v2m6.364.636-1.414 1.414M20 12h-2m-.222 6.364-1.414-1.414M12 20v-2m-6.364-.586 1.414-1.414M4 12h2m.222-6.364 1.414 1.414"/>
            </svg>

            Pending Review

        </div>

    @endif

</div>


    </div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800
            px-4 py-3 rounded-xl text-sm">

    {{ session('success') }}

</div>
@endif


   

{{-- ================= COMPANY PROFILE ================= --}}
<div class="space-y-6">

    {{-- HERO CARD --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Top Bar --}}
        <div class="relative h-20 {{ $level === 'Basic'
        ? 'bg-gradient-to-r from-gray-50 via-white to-gray-50'
        : '' }}

    {{ $level === 'Silver'
        ? 'bg-gradient-to-tr from-slate-300 via-gray-200 to-slate-100'
        : '' }}

    {{ $level === 'Gold'
        ? 'bg-gradient-to-tl from-white via-[#f7f3ec] to-[#e1d8cb]'
        : '' }}

    {{ $level === 'Platinum'
        ? 'bg-gradient-to-tl from-slate-950 via-gray-800 to-slate-600'
        : '' }} border-b border-gray-200">


                        <div class="absolute bottom-3 right-4 inline-flex items-center gap-1.5 px-4 py-1.5
                                            text-[11px] font-medium text-gray-500
                                            bg-[#f4f1eb] border border-gray-200
                                            rounded-full shadow-sm tracking-wide">

                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none"
                                stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>

                            {{ $company->years_on_platform ?? 0 }}+ years on Acrovoy
                        </div>



        </div>

        
        {{-- Content --}}
        <div class="relative px-8 pb-8">

           

            {{-- Company Header --}}
<div class="flex flex-col lg:flex-row lg:items-end gap-6">



    {{-- Logo --}}
    <div class="-mt-12 relative shrink-0">
        <div id="logo-dropzone"
            class="w-36 h-36 rounded-2xl overflow-hidden border-4 border-white shadow-lg bg-white relative cursor-pointer group">

            <img id="logo-preview"
                src="{{ $company->logo()?->cdn_url ?? asset('images/no-logo.png') }}"

                class="w-full h-full object-cover"
            >

            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center
                        opacity-0 group-hover:opacity-100 transition rounded-xl">
                <span class="text-white text-xs">Change</span>
            </div>

        </div>

        <input type="file" name="logo" accept="image/*" id="logo-input" class="hidden">

        

    </div>

    {{-- Company Info --}}
    <div  x-data="{ openReviews: false }" class="flex-1 pb-1">

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">

            <div class="space-y-3">

                <div>
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                                <h1 class="text-3xl font-bold text-gray-900 leading-tight">
                                    {{ $company->name }}
                                </h1>
                                @if($company->is_verified)
                                <img src="{{ asset('images/icons/verified_icon.png') }}"
                                    alt="Verified"
                                    class="w-5 h-5 flex-shrink-0">
                                @endif
            </div>
            
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] whitespace-nowrap font-semibold tracking-wide shadow-xl
                                    {{ $level === 'Basic' ? 'bg-gray-50 text-gray-400 border border-gray-200' : '' }}
                                    {{ $level === 'Silver' ? 'bg-gray-200 text-gray-700 border border-gray-300' : '' }}
                                    {{ $level === 'Gold' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                    {{ $level === 'Platinum' ? 'bg-slate-800 text-white border border-slate-700' : '' }}
                                ">

                            @if($level === 'Basic')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8" />
                            </svg>
                            @elseif($level === 'Silver')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M12 3l7 18H5l7-18z" />
                            </svg>
                            @elseif($level === 'Gold')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M5 12l5 5L20 7" />
                            </svg>
                            @elseif($level === 'Platinum')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M12 2l3 7h7l-5.5 4.2L18 21l-6-4-6 4 1.5-7.8L2 9h7z" />
                            </svg>
                            @endif

                            {{ strtoupper($level) }} BUYER
                        </span>



                       

                        


                       

                    </div>



                  



                </div>

                @if($company->short_description)

                    <p class="max-w-3xl text-gray-700 leading-relaxed">
                        {{ $company->short_description }}
                    </p>

                @endif

            </div>

        </div>

    </div>



    
</div>




        </div>



        

    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="space-y-5">

        {{-- ================= IDENTITY ================= --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-200 flex items-start justify-between">

    <div>
    <h2 class="text-lg font-semibold text-gray-900">
        @if($is_personal)
            Business Overview
        @else
            Company Overview
        @endif
    </h2>

    <p class="mt-1 text-sm text-gray-500">
        @if($is_personal)
            Basic information about your supplier profile and business activities.
        @else
            Basic company information and business description.
        @endif
    </p>
</div>

    <button
        type="button"
        onclick="openDrawer({
            title: 'Edit Company Overview',
            url: '{{ route("buyer.company.drawer","overview") }}'
            })"
        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50 transition">

        <svg class="w-4 h-4"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15.232 5.232l3.536 3.536M9 11l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 14.536A4 4 0 019.707 15.707L7 16l.293-2.707A4 4 0 018.464 10.88L15.232 5.232z"/>
        </svg>

        Edit

    </button>

</div>

    <div class="p-6 grid lg:grid-cols-3 gap-10">

        {{-- Description --}}
        <div class="lg:col-span-2 space-y-8">

            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                    Short Description
                </div>

                <div class="text-gray-800 leading-relaxed">
                    {{ $company->short_description ?? '—' }}
                </div>

            </div>

            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
    @if($is_personal)
        Supplier Description
    @else
        Company Description
    @endif
</div>

                <div class="prose prose-sm max-w-none text-gray-700">
                    {!! $company->description ?? '—' !!}
                </div>

            </div>

        </div>

        {{-- Company Profile --}}
<div>

    <div class="rounded-xl border border-gray-200 overflow-hidden">

    <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
        <div class="font-semibold text-gray-900">
            @if($is_personal)
                Business Identity
            @else
                Company Identity
            @endif
        </div>
    </div>

    <div class="divide-y divide-gray-200">

        {{-- Business Type --}}
        <div class="px-5 py-4">

            <div class="text-sm text-gray-500 mb-3">
                Business Type
            </div>

            @if($company->businessTypes->isNotEmpty())

                <div class="flex flex-wrap gap-2">

                    @foreach($company->businessTypes as $type)

                        <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                            {{ $type->translation?->name ?? $type->slug }}
                        </span>

                    @endforeach

                </div>

            @else

                <div class="text-sm text-gray-400">
                    —
                </div>

            @endif

        </div>


        {{-- Country --}}
        <div class="flex justify-between items-start px-5 py-4">

            <span class="text-sm text-gray-500">
                @if($is_personal)
                    Business Country
                @else
                    Registration Country
                @endif
            </span>

            <span class="text-sm font-medium text-gray-900 text-right">
                {{ $company->country?->name ?? '—' }}
            </span>

        </div>

    </div>

</div>

</div>

    </div>

</div>



{{-- ================= FACTORY PHOTO BLOCK ================= --}}

<div x-data="FactoryUploader()"
     class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200">

        <div class="flex justify-between items-center">

            <div>

    <h2 class="text-lg font-semibold text-gray-900">
        @if($is_personal)
            Business Photos
        @else
            Company Photos
        @endif
    </h2>

    <p class="mt-1 text-sm text-gray-500">
        @if($is_personal)
            Showcase your business activities and professional presence.
        @else
            Showcase your production facilities and manufacturing environment.
        @endif
    </p>

</div>

            <button
                onclick="openModal('factoryPhotosModal')"
                class="px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50 transition">

                + Add Photo

            </button>

        </div>

    </div>

    {{-- Body --}}
    <div class="p-6">

        <div class="grid md:grid-cols-4 gap-5">

            @forelse($company->factoryPhotos as $photo)

                <div class="relative group overflow-hidden rounded-xl border border-gray-200 bg-gray-50">

                    <img
                        src="{{ $photo->cdn_url }}"
                        class="aspect-square w-full object-cover transition duration-300 group-hover:scale-[1.02]">

                    <button
                        type="button"
                        @click="deletePhoto({{ $photo->id }})"
                        class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded-lg shadow">

                        Delete

                    </button>

                </div>

            @empty

                <div class="col-span-4 py-10 text-center text-sm text-gray-400">
    @if($is_personal)
        No supplier photos uploaded.
    @else
        No facility photos uploaded.
    @endif
</div>

            @endforelse

        </div>

    </div>

</div>

{{-- ================= Factory photo MODAL ================= --}}

<div id="factoryPhotosModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden">

        

        <div class="max-h-[70vh] overflow-y-auto">

            @include('dashboard.supplier.partials.manage-factoryphotos')

        </div>

    </div>

</div>




        {{-- ================= GENERAL INFORMATION ================= --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200 flex items-start justify-between gap-4">

    <div>

    <h2 class="text-lg font-semibold text-gray-900">
        @if($is_personal)
            Business Information
        @else
            Company Information
        @endif
    </h2>

    <p class="mt-1 text-sm text-gray-500">
        @if($is_personal)
            Business background, export markets and supplier details.
        @else
            Company background, export markets and business details.
        @endif
    </p>

</div>

    <button
        type="button"
        onclick="openDrawer({
            title: 'Edit General Information',
            description: 'Update company background and export markets.',
            url: '{{ route('buyer.company.drawer', 'general') }}'
        })"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:border-gray-300">

        <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.232 5.232l3.536 3.536M9 11l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 14.536A4 4 0 019.707 15.707L7 16l.293-2.707A4 4 0 018.464 10.88L15.232 5.232z"/>

        </svg>

        Edit

    </button>

</div>

    <div class="p-6 grid lg:grid-cols-2 gap-10">

        {{-- Left --}}
        <div class="space-y-8">

            {{-- About --}}
            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
    @if($is_personal)
        About Business
    @else
        About Company
    @endif
</div>

                <div class="text-sm text-gray-700 leading-7">
                    {!! $company->profile?->about_us_description ?? '—' !!}
                </div>

            </div>

           

        </div>
@if($is_personal)
@else
        {{-- Right --}}
        <div>

            <div class="grid grid-cols-2 gap-4">

                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Founded
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        {{ $company->profile?->founded_year ?? '—' }}
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Employees
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        {{ $company->profile?->total_employees ?? '—' }}
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Export Revenue
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        {{ $company->profile?->annual_export_revenue ? '$'.number_format($company->profile?->annual_export_revenue) : '—' }}
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Registration Capital
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        {{ $company->profile?->registration_capital ? '$'.number_format($company->profile?->registration_capital) : '—' }}
                    </div>
                </div>

            </div>

        </div>
        @endif

    </div>

</div>





{{-- ================= CERTIFICATES BLOCK ================= --}}

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mt-6">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200">

        <div class="flex items-center justify-between">

            <div class="flex items-center gap-2">

                <div>
    <h2 class="text-lg font-semibold text-gray-900">
        Certificates
    </h2>

    <p class="mt-1 text-sm text-gray-500">
        @if($is_personal)
            Manage your supplier certificates and compliance documents.
        @else
            Manage company certificates and compliance documents.
        @endif
    </p>
</div>

                <x-help-tooltip width="w-80">
    <div class="space-y-2 leading-relaxed">

        <div class="font-semibold text-white">
            Certifications
        </div>

        <div class="text-gray-200 text-sm normal-case">
            These certificates and documents verify the qualification and compliance of your
            @if($is_personal)
                supplier profile.
            @else
                company.
            @endif

            You can add new certificates, as well as view and manage existing ones.
        </div>

        <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1 normal-case">
            <li>Each certificate has a unique identification number (#).</li>
            <li>Certificate files must be uploaded in PDF or JPG format.</li>
            <li>Always check validity dates: "Valid from" and "Valid until".</li>
        </ul>

        <div class="text-gray-400 text-xs border-t border-gray-700 pt-2 normal-case">
            Recommendation: Upload only official documents that confirm your business status and compliance.
        </div>

    </div>
</x-help-tooltip>

            </div>

            <button
                type="button"
                onclick="openCertificateModal()"
                class="px-4 py-2 text-sm rounded-lg border border-gray-300 bg-white hover:bg-gray-50 transition">
                + Add Certificate
            </button>

        </div>

    </div>

    <div class="p-6">

        @php
            $certificates = $company->certificatesMedia()->get();
        @endphp

        <div class="flex items-center gap-4">

    {{-- Left --}}
    <button
        class="cert-prev flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-500 transition hover:bg-gray-50 hover:text-gray-700">

        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>

    </button>

    {{-- Slider --}}
    <div class="swiper certificates-swiper flex-1 overflow-hidden">

        <div class="swiper-wrapper">

        @foreach($certificates as $certificate)

            @php
                $meta = is_array($certificate->metadata)
                    ? $certificate->metadata
                    : json_decode($certificate->metadata ?? '{}', true);

                $certificateName = $meta['certificate_name'] ?? '';
                $certificateNumber = $meta['certificate_number'] ?? '';
                $validFrom = $meta['valid_from'] ?? '';
                $validUntil = $meta['valid_until'] ?? '';
            @endphp

            <div class="swiper-slide w-[190px] shrink-0 certificate-card data-id='{{ $certificate->id }}'">

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

                    {{-- Preview --}}
<div class="flex h-62 items-center justify-center bg-gray-50 p-4">

    @if(file_exists(storage_path('app/public/' . $certificate->previewPath())))

        @php
            $thumbUrl = asset('storage/' . $certificate->variantPath('thumb'));
        @endphp

        <div class="flex h-full w-full items-center justify-center rounded-md bg-white shadow-sm">

            <img
                src="{{ $thumbUrl }}"
                alt="{{ $certificateName }}"
                class="certificate-thumb max-h-[92%] max-w-[92%] cursor-pointer object-contain"
                data-index="{{ $loop->index }}">

        </div>

    @else

        <div class="flex h-full w-full items-center justify-center rounded-md bg-white text-xs text-gray-400">
            No Preview
        </div>

    @endif

</div>

                    {{-- Footer --}}
                    <div class="border-t border-gray-100 px-3 py-2.5">
<div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1 truncate text-sm font-semibold text-gray-900">
    {{ $certificateName ?: $certificate->original_file_name }}
</div>
                        <button
    class="delete-certificate-btn shrink-0 text-red-400 transition hover:text-red-600 text-xs"
    data-id="{{ $certificate->id }}">
    ✕
</button>
                            </div>

                        @if($certificateNumber)
    <div class="mt-2 flex items-center gap-2">
        <div
            class="min-w-0 flex-1 truncate text-xs text-gray-500"
            title="{{ $certificateNumber }}">
            {{ $certificateNumber }}
        </div>

        <button
            type="button"
            class="copy-certificate-number shrink-0 text-gray-400 transition hover:text-gray-700"
            data-number="{{ $certificateNumber }}"
            title="Copy certificate number">

            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="9" y="9" width="11" height="11" rx="2"/>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
            </svg>

        </button>
    </div>
@endif

                        @if($validUntil)
                            <div class="mt-1 text-xs text-gray-400">
                                Valid until {{ $validUntil }}
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        @endforeach

      </div>

    </div>

    {{-- Right --}}
    <button
        class="cert-next flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-500 transition hover:bg-gray-50 hover:text-gray-700">

        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>

    </button>

</div>

<div class="col-span-4 py-10 text-center text-sm text-gray-400">
    @if($is_personal)
        No supplier certificates uploaded.
    @else
        No company certificates uploaded.
    @endif
</div>

    </div>

</div>

{{-- ================= CERTIFICATE MODAL ================= --}}

<div id="certificateFormModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center">

    {{-- Overlay --}}
    <div
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
        onclick="closeCertificateModal()"
    ></div>

    {{-- Modal --}}
    <div class="relative z-10 w-full max-w-2xl max-h-[90vh] rounded-2xl overflow-hidden shadow-2xl bg-white flex flex-col">

        {{-- HEADER --}}
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 via-[#f4f1eb] to-[#ebe5dc] border-b border-gray-200">

            <div class="flex items-center justify-between">

                <div>

    <h2 class="text-xl font-semibold text-gray-900">
        Add Certification
    </h2>

    <p class="mt-1 text-sm text-gray-500">
        Upload official compliance documents for your
        @if($is_personal)
            supplier profile.
        @else
            company profile.
        @endif
    </p>

</div>

                <button
                    type="button"
                    onclick="closeCertificateModal()"
                    class="text-gray-500 hover:text-gray-900 transition text-xl leading-none">

                    ✕

                </button>

            </div>

        </div>

        {{-- BODY --}}
        <div class="flex-1 overflow-y-auto px-8 py-6 bg-white space-y-6">

            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Certificate Name
                </label>

                <input
                    id="certificateName"
                    type="text"
                    placeholder="Certificate Name"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

            </div>


            <div class="grid md:grid-cols-2 gap-5">

    {{-- Certificate Type --}}
    <div>

        <label class="block text-sm font-medium text-gray-700 mb-2">
            Certificate Type
        </label>

        <select
            id="certificateType"
            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

            <option value="">Select Certificate Type</option>
            <option value="ISO">ISO</option>
            <option value="FSC">FSC</option>
            <option value="ECO">ECO</option>
            <option value="CE">CE</option>
            <option value="OTHER">Other</option>

        </select>

    </div>

    {{-- Certificate Number --}}
    <div>

        <label class="block text-sm font-medium text-gray-700 mb-2">
            Certificate Number
        </label>

        <input
            id="certificateNumber"
            type="text"
            placeholder="Certificate Number"
            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

    </div>

</div>


            <div class="grid md:grid-cols-2 gap-5">

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Valid From
                    </label>

                    <input
                        id="validFrom"
                        type="date"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                </div>

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Valid Until
                    </label>

                    <input
                        id="validUntil"
                        type="date"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400">

                </div>

            </div>


            <div>

    <label class="block text-sm font-medium text-gray-700 mb-2">
        Certificate File
    </label>

    <input
        id="certificateFile"
        type="file"
        accept=".jpg,.jpeg,.png,.webp,.pdf"
        class="hidden">

    <label
        for="certificateFile"
        class="group relative flex flex-col items-center justify-center w-full rounded-2xl 
        border-2 border-dashed border-gray-300 bg-gradient-to-b from-gray-50 to-white 
        px-6 py-5 cursor-pointer transition hover:border-gray-400 hover:bg-gray-50">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-white border border-gray-200 shadow-sm group-hover:shadow-md transition">

            <svg class="w-7 h-7 text-gray-500"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M12 12v8m0-8l-3 3m3-3l3 3"/>
            </svg>

        </div>

        <div class="mt-5 text-base font-semibold text-gray-900">
            Choose certificate
        </div>

        <div class="mt-1 text-sm text-gray-500">
            or drag & drop it here
        </div>

        <div class="mt-4 text-xs text-gray-400">
            PDF, JPG, PNG or WEBP • Max 10 MB
        </div>

    </label>

    {{-- имя выбранного файла --}}
    <div
        id="certificateFileName"
        class="hidden mt-3 text-sm text-gray-600 font-medium">
    </div>

</div>

        </div>

        {{-- FOOTER --}}
        <div class="px-8 py-5 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">

            

            <button
                type="button"
                onclick="submitCertificate()"
                class="px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black transition">

                Upload Certificate

            </button>

        </div>

    </div>

</div>








       

        {{-- ================= CONTACT INFORMATION ================= --}}


   <x-contact.list
    :contacts="$company->contacts"
    title="Business Contacts"
    description="Primary business contact details."
    ownerType="supplier"
    :ownerId="$company->id"
    :editable="true"
    
/>



    

</div>
@if($is_personal)
@else
{{-- ================= Team Members ================= --}}

<div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                Team Members
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Users who have access to manage this company.
            </p>

        </div>

        <a
    href="{{ route('supplier.team.members') }}"
    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">

    <svg
        class="h-4 w-4"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24">

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-8 0v2m8 0H9m4-10a4 4 0 100-8 4 4 0 000 8z"/>

    </svg>

    Manage Team

</a>

    </div>

    {{-- Members --}}
    <div class="divide-y divide-gray-100">

        @forelse($company->members()->with('user')->get() as $member)

            <div class="flex items-center justify-between px-6 py-5">

                <div class="flex items-center gap-4">

    @if($member->user->avatar()?->cdn_url)

        <img
            src="{{ $member->user->avatar()->cdn_url }}"
            alt="{{ $member->user->name }}"
            class="h-12 w-12 rounded-full object-cover border border-gray-200">

    @else

        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-lg font-semibold text-gray-700">

            {{ strtoupper(substr($member->user->name ?? '?', 0, 1)) }}

        </div>

    @endif

    <div>

        <div class="font-medium text-gray-900">

            {{ trim(($member->user->name ?? '') . ' ' . ($member->user->last_name ?? '')) }}

        </div>

        <div class="mt-1 text-sm text-gray-500">

            {{ $member->user->email }}

        </div>

    </div>

</div>

                <div class="flex items-center gap-3 flex-wrap">

    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium capitalize text-gray-700">
        {{ str_replace('_', ' ', $member->role) }}
    </span>

    @if($member->status === 'active')
        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
            Active
        </span>
    @else
        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">
            Pending
        </span>
    @endif

    @if($member->show_in_profile)
        <span class="rounded-full bg-blue-50 border border-blue-200 px-3 py-1 text-xs font-medium text-blue-700">
            Visible on Company Profile
        </span>
    @else
        <span class="rounded-full bg-gray-50 border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500">
            Hidden from Company Profile
        </span>
    @endif

</div>

            </div>

        @empty

            <div class="px-6 py-10 text-center">

                <svg
                    class="mx-auto mb-4 h-10 w-10 text-gray-300"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-8 0v2m8 0H9m4-10a4 4 0 100-8 4 4 0 000 8z"/>

                </svg>

                <h3 class="text-sm font-semibold text-gray-900">
                    No team members yet
                </h3>

                <p class="mt-2 text-sm text-gray-500">
                    Invite colleagues to collaborate on your company profile.
                </p>

            </div>

        @endforelse

    </div>

</div>

@endif


{{-- ================= ADDRESS ================= --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200 flex items-start justify-between">

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                @if($is_personal)
                    Business Address
                @else
                    Company Address
                @endif
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                @if($is_personal)
                    Your business location and contact address.
                @else
                    Company registered address and business location.
                @endif
            </p>

        </div>

        <button
            type="button"
            onclick="openDrawer({
                title: 'Edit Address',
                url: '{{ route('supplier.company.drawer', 'address') }}'
            })"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">

            <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15.232 5.232l3.536 3.536M9 11l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 14.536A4 4 0 019.707 15.707L7 16l.293-2.707A4 4 0 018.464 10.88L15.232 5.232z"/>

            </svg>

            Edit

        </button>

    </div>

    @php
        $address = $company->primaryAddress;

        $hasAddress =
            $address &&
            (
                filled($address->country_id) ||
                filled($address->state) ||
                filled($address->city) ||
                filled($address->postal_code) ||
                filled($address->address_line_1) ||
                filled($address->address_line_2)
            );
    @endphp

    @if($hasAddress)

        <div class="divide-y divide-gray-200">

            <div class="flex justify-between items-start px-6 py-4">

                <span class="text-sm text-gray-500">
                    Country
                </span>

                <span class="text-sm font-medium text-gray-900 text-right">
                    {{ $address->country?->name ?? '—' }}
                </span>

            </div>

            @if(filled($address->state))
            <div class="flex justify-between items-start px-6 py-4">

                <span class="text-sm text-gray-500">
                    State / Province
                </span>

                <span class="text-sm font-medium text-gray-900 text-right">
                    {{ $address->state }}
                </span>

            </div>
            @endif

            @if(filled($address->city))
            <div class="flex justify-between items-start px-6 py-4">

                <span class="text-sm text-gray-500">
                    City
                </span>

                <span class="text-sm font-medium text-gray-900 text-right">
                    {{ $address->city }}
                </span>

            </div>
            @endif

            @if(filled($address->postal_code))
            <div class="flex justify-between items-start px-6 py-4">

                <span class="text-sm text-gray-500">
                    Postal Code
                </span>

                <span class="text-sm font-medium text-gray-900 text-right">
                    {{ $address->postal_code }}
                </span>

            </div>
            @endif

            @if(filled($address->address_line_1))
            <div class="flex justify-between items-start px-6 py-4">

                <span class="text-sm text-gray-500">
                    Street Address
                </span>

                <span class="text-sm font-medium text-gray-900 text-right max-w-sm">
                    {{ $address->address_line_1 }}
                </span>

            </div>
            @endif

            @if(filled($address->address_line_2))
            <div class="flex justify-between items-start px-6 py-4">

                <span class="text-sm text-gray-500">
                    Building / Office
                </span>

                <span class="text-sm font-medium text-gray-900 text-right max-w-sm">
                    {{ $address->address_line_2 }}
                </span>

            </div>
            @endif

        </div>

    @else

        <div class="px-6 py-12 text-center">

            <div class="text-sm text-gray-400">
                No address information has been added yet.
            </div>

        </div>

    @endif

     @if($address?->latitude && $address?->longitude)

<div class="border-t border-gray-200">

    <div class="px-6 py-4">

        <div class="mb-3 flex items-center justify-between">

            <div>

                <div class="text-sm font-semibold text-gray-900">
                    Location Preview
                </div>

                <div class="text-xs text-gray-500">
                    This is how your business location will appear to buyers.
                </div>

            </div>

            <a
                href="https://www.google.com/maps/search/?api=1&query={{ $address->latitude }},{{ $address->longitude }}"
                target="_blank"
                class="text-sm font-medium text-blue-600 hover:text-blue-700">

                Open in Google Maps

            </a>

        </div>

        <div 
    id="company-address-map"
    class="h-48 w-full overflow-hidden rounded-xl border border-gray-200"
    data-lat="{{ $address->latitude }}"
    data-lng="{{ $address->longitude }}">
</div>

    </div>

</div>

@endif

</div>






 @php
    $certGallery = [];

    foreach ($company->certificatesMedia as $certificate) {

    $src = $certificate->cdn_url;

    $thumb = asset('storage/' . $certificate->variantPath('thumb'));

    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg','jpeg','png','gif','webp','avif'])) {
    $type = 'image';
    } elseif (in_array($ext, ['mp4','webm','mov'])) {
    $type = 'video';
    } elseif ($ext === 'pdf') {
    $type = 'pdf';
    } else {
    $type = 'file';
    }

    $certGallery[] = [
    'type' => $type,
    'src' => $src,
    'thumb' => $thumb,
    ];
    }
    @endphp


<script> 

   document.addEventListener('click', async function (e) {

    const btn = e.target.closest('.copy-certificate-number');

    if (!btn) return;

    const number = btn.dataset.number;

    try {

        await navigator.clipboard.writeText(number);

        dispatchAlert(
            'success',
            'Certificate number copied to clipboard.'
        );

    } catch (err) {

        dispatchAlert(
            'error',
            'Failed to copy certificate number.'
        );

    }

});

</script>

<script>
document.getElementById('certificateFile').addEventListener('change', function () {

    const name = document.getElementById('certificateFileName');

    if (this.files.length) {
        name.textContent = this.files[0].name;
        name.classList.remove('hidden');
    } else {
        name.classList.add('hidden');
        name.textContent = '';
    }

});
</script>

<script>

/* ================= MODAL CONTROL ================= */

function openCertificateModal() {
    document.getElementById('certificateFormModal').classList.remove('hidden');
}

function closeCertificateModal() {
    document.getElementById('certificateFormModal').classList.add('hidden');

    document.getElementById('certificateName').value = '';
    document.getElementById('certificateNumber').value = '';
    document.getElementById('validFrom').value = '';
    document.getElementById('validUntil').value = '';
    document.getElementById('certificateFile').value = '';
}

/* ================= SUBMIT CERTIFICATE ================= */

function submitCertificate() {

    const file = document.getElementById('certificateFile').files[0];

    if (!file) {
        alert("Select certificate file");
        return;
    }

    const metadata = {
        certificate_name: document.getElementById('certificateName').value,
        certificate_type: document.getElementById('certificateType').value,
        certificate_number: document.getElementById('certificateNumber').value,
        valid_from: document.getElementById('validFrom').value,
        valid_until: document.getElementById('validUntil').value
    };

    const formData = new FormData();

    formData.append('certificate', file);
    formData.append('metadata', JSON.stringify(metadata));

    fetch("{{ route('manufacturer.certificates.upload') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(response => {

        if (response.success) {

            closeCertificateModal();
            location.reload();
        } else {
            alert(response.message || 'Upload failed');
        }

    })
    .catch(() => alert("Upload failed"));
}

/* ================= DELETE CERTIFICATE ================= */

document.addEventListener('click', function(e){

    if(e.target?.classList.contains('delete-certificate-btn')){

        deleteCertificate(e.target.dataset.id);
    }

});

function deleteCertificate(id) {
    console.log('delete', id);

    const url = "{{ route('manufacturer.certificates.delete', ['certificate' => 'CERT_ID']) }}"
        .replace('CERT_ID', id);

    fetch(url, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}",
        'Accept': 'application/json'
    }
})
.then(async res => {

    console.log(res.status);

    const data = await res.json();

    console.log(data);

    return data;   // <-- обязательно вернуть

})
.then(data => {

    if (data.success) {
        location.reload();
    }

})
.catch(err => {
    console.error(err);
    alert('Delete failed');
});
}

</script>








 


    

</div>







<script>

    function FactoryUploader() {
    return {
        async deletePhoto(id) {

            if (!confirm('Delete this photo?')) return;

            try {

                const response = await fetch(`/factory/photos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                console.log('Delete response:', data);

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Delete failed');
                }

            } catch (e) {
                console.error(e);
                alert('Request error');
            }
        }
    }
}

</script>

<script>
function openModal(id){
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id){
    document.getElementById(id)?.classList.add('hidden');
}
</script>

@vite('resources/js/product-gallery.js')
<x-media-viewer id="productViewer"></x-media-viewer>
<script>
    window.certificatesGallery = @json($certGallery);
    
</script>

{{-- JS для drag&drop превью --}}
<script>
const logoInput = document.getElementById('logo-input');
const logoPreview = document.getElementById('logo-preview');
const dropzone = document.getElementById('logo-dropzone');

// Клик по зоне открывает выбор файла
dropzone.addEventListener('click', () => logoInput.click());

// Выбор файла
logoInput.addEventListener('change', function (event) {

    const [file] = event.target.files;

    if (!file) return;

    logoPreview.src = URL.createObjectURL(file);

    uploadLogo(file);

});

// Drag & Drop
dropzone.addEventListener('dragover', (e) => {

    e.preventDefault();

    dropzone.classList.add('border-blue-400', 'bg-blue-50');

});

dropzone.addEventListener('dragleave', (e) => {

    e.preventDefault();

    dropzone.classList.remove('border-blue-400', 'bg-blue-50');

});

dropzone.addEventListener('drop', (e) => {

    e.preventDefault();

    dropzone.classList.remove('border-blue-400', 'bg-blue-50');

    const file = e.dataTransfer.files[0];

    if (!file) return;

    logoInput.files = e.dataTransfer.files;

    logoPreview.src = URL.createObjectURL(file);

    uploadLogo(file);

});

async function uploadLogo(file)
{
    const formData = new FormData();
    formData.append('logo', file);

    try {

        const response = await fetch(
            "{{ route('buyer.company.logo') }}",
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                    'Accept': 'application/json'
                },
                body: formData
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message ?? 'Upload failed');
        }

       // Обновляем изображение на URL с сервера
if (data.url) {
    logoPreview.src = data.url + '?t=' + Date.now();
}

dispatchAlert('success', data.message);

    } catch (e) {

        console.error(e);

        dispatchAlert('error', e.message ?? 'Upload failed.');

    }
}
</script>

<x-edit.edit-drawer />

<x-alerts />

@endsection