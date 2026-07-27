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
        <div class="h-20 bg-gradient-to-r from-[#e9decb] via-[#f5efe5] to-[#fffdf9] border-b border-gray-200"></div>

        {{-- Content --}}
        <div class="relative px-8 pb-8">

           

            {{-- Company Header --}}
<div class="flex flex-col lg:flex-row lg:items-end gap-6">

    {{-- Logo --}}
    <div class="-mt-12 relative shrink-0">
        <div id="logo-dropzone"
            class="w-36 h-36 rounded-2xl overflow-hidden border-4 border-white shadow-lg bg-white relative cursor-pointer group">

            <img id="logo-preview"
                src="{{ $company->logo?->cdn_url ?? asset('images/no-logo.png') }}"
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
    <div class="flex-1 pb-1">

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">

            <div class="space-y-3">

                <div>

                    <h1 class="text-3xl font-bold text-gray-900 leading-tight">
                        {{ $company->name }}
                    </h1>

                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">

                        @if($company->country)
                            <span>{{ $company->country->name }}</span>
                        @endif

                        @if($company->country)
                            <span>•</span>
                        @endif

                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-xs font-medium">
                            Verified Supplier
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
            Company Overview
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Basic information and company description.
        </p>
    </div>

    <button
        type="button"
        onclick="openDrawer({
            title: 'Edit Company Overview',
            url: '{{ route("supplier.company.drawer","overview") }}'
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
                    Company Description
                </div>

                <div class="prose prose-sm max-w-none text-gray-700">
                    {!! $company->description ?? '—' !!}
                </div>

            </div>

        </div>

        {{-- Company Details --}}
        <div>

            <div class="rounded-xl border border-gray-200 overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="font-semibold text-gray-900">
                        Company Details
                    </div>
                </div>

                <div class="divide-y divide-gray-200">

                    <div class="flex justify-between items-start px-5 py-4">
                        <span class="text-sm text-gray-500">
                            Company Name
                        </span>

                        <span class="text-sm font-medium text-gray-900 text-right">
                            {{ $company->name }}
                        </span>
                    </div>

                    <div class="flex justify-between items-start px-5 py-4">
                        <span class="text-sm text-gray-500">
                            Registration Country
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



        {{-- ================= GENERAL INFORMATION ================= --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200 flex items-start justify-between gap-4">

    <div>

        <h2 class="text-lg font-semibold text-gray-900">
            General Information
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Company background, export markets and business details.
        </p>

    </div>

    <button
        type="button"
        onclick="openDrawer({
            title: 'Edit General Information',
            description: 'Update company background and export markets.',
            url: '{{ route('supplier.company.drawer', 'general') }}'
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
                    About Us
                </div>

                <div class="text-sm text-gray-700 leading-7">
                    {!! $company->profile?->about_us_description ?? '—' !!}
                </div>

            </div>

            {{-- Export Markets --}}
            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                    Export Markets
                </div>

                <div class="flex flex-wrap gap-2">

                    @forelse($company->exportMarkets as $market)

                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $market->translation?->name ?? $market->slug }}
                        </span>

                    @empty

                        <span class="text-gray-400">
                            —
                        </span>

                    @endforelse

                </div>

            </div>

        </div>

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

    </div>

</div>



        {{-- ================= MANUFACTURING PROFILE ================= --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

   {{-- Header --}}
<div class="flex items-start justify-between px-6 py-5 border-b border-gray-200">

    <div>

        <h2 class="text-lg font-semibold text-gray-900">
            Manufacturing Profile
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Production capabilities, factory information and manufacturing capacity.
        </p>

    </div>

    <button
        type="button"
        onclick="openDrawer({
            title: 'Edit Manufacturing Profile',
            description: 'Update manufacturing facilities and production capacity.',
            url: '{{ route('supplier.company.drawer', 'manufacturing') }}'
        })"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:border-gray-400">

        <svg
            class="h-4 w-4"
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

    <div class="p-6 space-y-8">

        {{-- Manufacturing Capabilities --}}
        @if($company->profile?->manufacturingCapabilities?->isNotEmpty())

            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                    Manufacturing Capabilities
                </div>

                <div class="flex flex-wrap gap-2">

                    @foreach($company->profile->manufacturingCapabilities as $capability)

                        <span class="px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-medium">
                            {{ $capability->name }}
                        </span>

                    @endforeach

                </div>

            </div>

        @endif


        {{-- Manufacturing Overview --}}
        <div>

            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                Manufacturing Overview
            </div>

            <div class="text-sm leading-7 text-gray-700">
                {!! $company->profile?->manufacturing_description ?? '—' !!}
            </div>

        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

            <div class="rounded-xl border border-gray-200 p-5">

                <div class="text-xs uppercase tracking-wide text-gray-400">
                    Factory Area
                </div>

                <div class="mt-2 text-lg font-bold text-gray-900">
                    {{ $company->profile?->factory_area ? $company->profile?->factory_area.' m²' : '—' }}
                </div>

            </div>

            <div class="rounded-xl border border-gray-200 p-5">

                <div class="text-xs uppercase tracking-wide text-gray-400">
                    Production Lines
                </div>

                <div class="mt-2 text-lg font-bold text-gray-900">
                    {{ $company->profile?->production_lines ?? '—' }}
                </div>

            </div>

            <div class="rounded-xl border border-gray-200 p-5">

                <div class="text-xs uppercase tracking-wide text-gray-400">
                    MOQ
                </div>

                <div class="mt-2 text-lg font-bold text-gray-900">
                    {{ $company->profile?->moq ?? '—' }}
                </div>

            </div>

            <div class="rounded-xl border border-gray-200 p-5">

                <div class="text-xs uppercase tracking-wide text-gray-400">
                    Monthly Capacity
                </div>

                <div class="mt-2 text-lg font-bold text-gray-900">
                    {{ $company->profile?->monthly_capacity ?? '—' }}
                </div>

            </div>

            <div class="rounded-xl border border-gray-200 p-5">

                <div class="text-xs uppercase tracking-wide text-gray-400">
                    Lead Time
                </div>

                <div class="mt-2 text-lg font-bold text-gray-900">
                    {{ $company->profile?->lead_time_days ? $company->profile?->lead_time_days.' days' : '—' }}
                </div>

            </div>

        </div>

    </div>

</div>

        {{-- ================= CONTACT INFORMATION ================= --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
<div class="px-6 py-5 border-b border-gray-200">

    <div class="flex items-start justify-between">

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                Contact Information
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Primary business contact details.
            </p>

        </div>

        <button
            type="button"
            onclick="openDrawer({
                title: 'Edit Contact Information',
                url: '{{ route('supplier.company.drawer', 'contacts') }}'
            })"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">

            <svg
                class="h-4 w-4"
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

</div>

    <div class="divide-y divide-gray-200">

        {{-- Email --}}
        <div class="flex flex-col md:flex-row md:items-center px-6 py-5">

            <div class="w-full md:w-56 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Email
            </div>

            <div class="flex-1 text-gray-900 font-medium break-all">
                {{ $company->email ?? '—' }}
            </div>

        </div>

        {{-- Phone --}}
        <div class="flex flex-col md:flex-row md:items-center px-6 py-5">

            <div class="w-full md:w-56 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Phone
            </div>

            <div class="flex-1 text-gray-900 font-medium">
                {{ $company->phone ?? '—' }}
            </div>

        </div>

        {{-- Address --}}
        <div class="flex flex-col md:flex-row px-6 py-5">

            <div class="w-full md:w-56 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Address
            </div>

            <div class="flex-1 text-gray-900 leading-7">
                {{ $company->address ?? '—' }}
            </div>

        </div>

    </div>

</div>

    </div>

</div>

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

                <div class="flex items-center gap-4">

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
                        Manage company certificates and compliance documents.
                    </p>
                </div>

                <x-help-tooltip width="w-80">
                    <div class="space-y-2 leading-relaxed">
                        <div class="font-semibold text-white">
                            Certificates
                        </div>

                        <div class="text-gray-200 text-sm normal-case">
                            Здесь отображаются все сертификаты, подтверждающие качество и соответствие продукции.
                            Вы можете добавлять новые сертификаты, а также просматривать и удалять существующие.
                        </div>

                        <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1 normal-case">
                            <li>Каждый сертификат имеет уникальный номер (#).</li>
                            <li>Файл сертификата должен быть в формате PDF или JPG.</li>
                            <li>Проверяйте сроки действия: «Valid from» и «Valid until».</li>
                        </ul>

                        <div class="text-gray-400 text-xs border-t border-gray-700 pt-2 normal-case">
                            Рекомендация: загружайте только официальные документы, чтобы избежать проблем при проверках.
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

        <div id="certificatesContainer"
             class="flex flex-wrap gap-4 justify-start items-start">

            @foreach($certificates as $certificate)

                @php
                    $meta = is_array($certificate->metadata)
                        ? $certificate->metadata
                        : json_decode($certificate->metadata ?? '{}', true);

                    $certificateName = $meta['certificate_name'] ?? '';
                    $certificateType = strtoupper($meta['certificate_type'] ?? '');

                    $upperName = strtoupper($certificateName ?: $certificate->original_file_name);

                    if (str_contains($upperName, 'ISO')) $badges[] = 'ISO';
                    if (str_contains($upperName, 'ECO')) $badges[] = 'ECO';
                    if (str_contains($upperName, 'FSC')) $badges[] = 'FSC';

                    $certificateNumber = $meta['certificate_number'] ?? '';
                    $validFrom = $meta['valid_from'] ?? '';
                    $validUntil = $meta['valid_until'] ?? '';
                @endphp

                <div class="certificate-card border border-gray-200 rounded-xl p-4 bg-gray-50 space-y-3 w-60">

                    {{-- Preview --}}
                    <div class="w-full aspect-[3/4] border border-gray-200 rounded-lg overflow-hidden bg-white flex items-center justify-center">

                        @if(file_exists(storage_path('app/public/' . $certificate->previewPath())))
                            <img
                                src="{{ asset('storage/' . $certificate->previewPath()) }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="text-gray-400 text-xs text-center p-3">
                                No Preview
                            </div>
                        @endif

                    </div>

                    {{-- Content --}}
                    <div class="space-y-2">

                        <div class="flex justify-between items-start gap-2">

                            <a href="javascript:void(0);"
                               class="certificate-thumb text-sm font-semibold text-gray-900 hover:text-blue-600 hover:underline line-clamp-2"
                               data-index="{{ $loop->index }}">
                                {{ $certificateName ?: $certificate->original_file_name }}
                            </a>

                            <button
                                class="delete-certificate-btn text-red-400 hover:text-red-600 transition text-xs"
                                data-id="{{ $certificate->id }}">
                                ✕
                            </button>

                        </div>

                        <div class="text-gray-400 text-[11px] break-all">
                            {{ $certificate->original_file_name }}
                        </div>

                        <div class="text-xs text-gray-500 space-y-1">

                            @if($certificateNumber)
                                <div># {{ $certificateNumber }}</div>
                            @endif

                            @if($validFrom)
                                <div>Valid from: {{ $validFrom }}</div>
                            @endif

                            @if($validUntil)
                                <div>Valid until: {{ $validUntil }}</div>
                            @endif

                        </div>

                    </div>

                </div>

            @endforeach

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
                        Add Certificate
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Upload a compliance certificate for your company.
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

    const url = "{{ route('manufacturer.certificates.delete', ['certificate' => 'CERT_ID']) }}"
        .replace('CERT_ID', id);

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {

            document.querySelector(
                `.delete-certificate-btn[data-id="${id}"]`
            )?.closest('.certificate-card')?.remove();
        }

    })
    .catch(() => alert('Delete failed'));
}

</script>


{{-- ================= FACTORY PHOTO BLOCK ================= --}}

<div x-data="FactoryUploader()"
     class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200">

        <div class="flex justify-between items-center">

            <div>

                <h2 class="text-lg font-semibold text-gray-900">
                    Factory Photos
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Showcase your production facilities and manufacturing environment.
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
                    No factory photos uploaded.
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






 {{-- ================= CATALOG VISUAL BLOCK ================= --}}

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mt-6">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200">

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                Catalog Presentation
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Choose the main image that will represent your company in the supplier catalog.
            </p>

        </div>

    </div>

    {{-- Body --}}
    <div class="p-8">

        <div class="grid lg:grid-cols-2 gap-12 items-start">

            {{-- Upload --}}
            <div class="flex flex-col items-center">

                <div
                    id="catalog-dropzone"
                    class="group w-72 aspect-square rounded-2xl overflow-hidden
                           border-2 border-dashed border-gray-300
                           bg-gradient-to-b from-gray-50 to-white
                           cursor-pointer relative transition hover:border-gray-400">

                    <img
                        id="catalog-preview"
                        src="{{ $catalogMedia?->cdn_url ?? asset('images/no-catalog-image.png') }}"
                        class="w-full h-full object-cover">

                    <div
                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center">

                        <div class="px-4 py-2 rounded-lg bg-white text-gray-900 text-sm font-medium shadow">
                            Change Image
                        </div>

                    </div>

                    <input
                        type="file"
                        name="catalog_image"
                        accept="image/*"
                        id="catalog-input"
                        class="hidden">

                </div>

                <p class="mt-4 text-sm text-gray-500 text-center max-w-xs">
                    This image will be displayed on supplier cards throughout the marketplace.
                </p>

            </div>

            {{-- Preview --}}
            <div class="flex justify-center items-start">

                @include('dashboard.supplier.partials.preview-card')

            </div>

        </div>

    </div>

</div>
    

</div>

<script>
document.getElementById('catalog-dropzone').addEventListener('click', function() {
    document.getElementById('catalog-input').click();
});

document.getElementById('catalog-input').addEventListener('change', function(e) {

    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('catalog_image', file);

    fetch("{{ route('manufacturer.catalog.upload') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            document.getElementById('catalog-preview').src = data.url;
        }

    })
    .catch(() => alert('Upload failed'));

});
</script>





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
            "{{ route('supplier.company.logo') }}",
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