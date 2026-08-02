



<div class="max-w-7xl mx-auto space-y-4">

@php
$company = $supplier;
            $level = $company->level;
            $supplierRating = round(
                        $company->supplierReviews->avg('rating') ?? 0,
                        1
                    );
                    
            @endphp



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
        <div class="w-36 h-36 rounded-2xl overflow-hidden border-4 border-white shadow-lg bg-white relative">

            <img src="{{ $company->logo?->cdn_url ?? asset('images/no-logo.png') }}"
                class="w-full h-full object-cover"
            >

            

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

                            {{ strtoupper($level) }} SUPPLIER
                        </span>



                        <div class="mt-1 items-center flex justify-center">
                            <div class="flex flex-col md:flex-row items-center gap-1 inline">

                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <=floor($supplierRating))
                                    <svg class="w-4 h-4 fill-current text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" /></svg>
                                    @elseif ($i - $supplierRating < 1)
                                        <svg class="w-4 h-4 fill-current text-yellow-300" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" /></svg>
                                        @else
                                        <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                        </svg></svg>

                                        @endif

                                        @endfor

                                        <span class="text-xs text-gray-500">{{$supplierRating = number_format($supplierRating, 1);}}</span>

                            </div>
                        </div>

                        <div @click="openReviews = true" class="items-center flex justify-center text-xs  mt-1 text-emerald-700 hover:text-emerald-900 hover:underline hover:cursor-pointer">
                            {{-- Количество отзывов --}}
                            <span>{{ $company->supplierReviews->count() }} review(s)</span>
                        </div>


                        <!-- @if($company->country)
                            <span>•</span>
                        @endif -->

                        <!-- <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-xs font-medium">
                            Verified Supplier
                        </span> -->

                    </div>



                    @include('supplier.modals.supplier_reviews', ['supplier' => $company])



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
        {{ $is_personal ? 'Business Overview' : 'Company Overview' }}
    </h2>

    <p class="mt-1 text-sm text-gray-500">
        {{ $is_personal
            ? 'Basic information about your business profile.'
            : 'Basic information and company description.'
        }}
    </p>
</div>

    

</div>

    <div class="p-6 grid lg:grid-cols-3 gap-10">

        {{-- Description --}}
        <div class="lg:col-span-2 space-y-8">

            

            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
    {{ $is_personal ? 'Business Description' : 'Company Description' }}
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
            {{ $is_personal ? 'Business Identity' : 'Company Identity' }}
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

        {{-- Registration Country --}}
        <div class="flex justify-between items-start px-5 py-4">

            <span class="text-sm text-gray-500">
                {{ $is_personal ? 'Business Location' : 'Registration Country' }}
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


@if($is_personal)
@else
{{-- ================= Team Members ================= --}}

<div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                Meet the Team 
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Our experienced team is here to support you.
            </p>

        </div>

      

    </div>

    {{-- Members --}}
<div class="p-6">

    <div class="flex flex-wrap gap-4">

        @forelse($company->profileMembers()->with('user')->get() as $member)

            <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm hover:shadow-md transition w-full md:w-[calc(50%-8px)] xl:w-[calc(33.333%-11px)]">

                {{-- Avatar --}}
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

                {{-- Info --}}
                <div class="flex-1 min-w-0">

                    <div class="font-medium text-gray-900 truncate">
                        {{ trim(($member->user->name ?? '') . ' ' . ($member->user->last_name ?? '')) }}
                    </div>

                    @if($member->user->profileContact)

                        <div class="mt-1 text-sm text-gray-500 truncate">
                            {{ $member->user->profileContact->display_value }}
                        </div>

                    @endif

                    <div class="mt-1">
                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 capitalize">
                            {{ str_replace('_', ' ', $member->role) }}
                        </span>
                    </div>

                </div>

                {{-- чат --}}
                <a href="#"
   class="flex h-10 w-10 items-center justify-center
       rounded-xl
       border border-gray-200
       bg-white
       text-gray-400
       hover:text-gray-900
       hover:border-gray-400
       transition">

    <svg class="w-5 h-5"
         fill="none"
         stroke="currentColor"
         stroke-width="1.8"
         viewBox="0 0 24 24">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M8 10h8M8 14h5M21 12a8 8 0 01-8 8H7l-4 2 1.2-4.2A8 8 0 013 12a8 8 0 018-8h2a8 8 0 018 8z"/>
    </svg>

</a>

            </div>

        @empty

            <div class="w-full py-10 text-center">

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

</div>

@endif




@php
            $factoryGallery = [];

            foreach ($company->factoryPhotos as $photo) {

                $src = $photo->cdn_url;
                $thumb = $photo->cdn_url;

                $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg','jpeg','png','gif','webp','avif'])) {
                    $type = 'image';
                } elseif (in_array($ext, ['mp4','webm','mov'])) {
                    $type = 'video';
                } else {
                    $type = 'image';
                }

                $factoryGallery[] = [
                    'type' => $type,
                    'src' => $src,
                    'thumb' => $thumb,
                ];
            }
            @endphp
@if(!empty($factoryGallery))

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

           

        </div>

    </div>

    {{-- Body --}}
    <div class="p-6">

        <div class="grid md:grid-cols-4 gap-5">

            @forelse($company->factoryPhotos as $photo)

                <div
    class="factory-thumb group relative aspect-square overflow-hidden rounded-xl border border-gray-200 bg-gray-50 cursor-pointer"
    data-index="{{ $loop->index }}">

    <img
        src="{{ $photo->cdn_url }}"
        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
        alt="Factory photo">

    <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 transition group-hover:opacity-100">

        <span class="text-sm font-medium text-white">
            Open
        </span>

    </div>

</div>

            @empty

                <div class="col-span-4 py-10 text-center text-sm text-gray-400">
                    No photos uploaded.
                </div>

            @endforelse

        </div>

    </div>

</div>


@endif



      {{-- ================= GENERAL INFORMATION ================= --}}
@php
    $profile = $company->profile;

    $hasLeft =
        filled($profile?->about_us_description) ||
        $company->exportMarkets->isNotEmpty();

    $hasRight =
        !$is_personal &&
        (
            filled($profile?->founded_year) ||
            filled($profile?->total_employees) ||
            filled($profile?->annual_export_revenue) ||
            filled($profile?->registration_capital)
        );
@endphp

@if($hasLeft || $hasRight)

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-200 flex items-start justify-between gap-4">

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                {{ $is_personal ? 'Business Information' : 'Company Information' }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                {{ $is_personal
                    ? 'Business background, export markets and supplier details.'
                    : 'Company background, export markets and business details.' }}
            </p>

        </div>

    </div>

    <div class="p-6 grid lg:grid-cols-2 gap-10">

        {{-- Left --}}
        @if($hasLeft)

        <div class="space-y-8">

            {{-- About --}}
            @if(filled($profile?->about_us_description))

            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                    {{ $is_personal ? 'About Me' : 'About Company' }}
                </div>

                <div class="text-sm text-gray-700 leading-7">
                    {!! $profile->about_us_description !!}
                </div>

            </div>

            @endif

            {{-- Export Markets --}}
            @if($company->exportMarkets->isNotEmpty())

            <div>

                <div class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                    Markets
                </div>

                <div class="flex flex-wrap gap-2">

                    @foreach($company->exportMarkets as $market)

                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $market->translation?->name ?? $market->slug }}
                        </span>

                    @endforeach

                </div>

            </div>

            @endif

        </div>

        @endif


        {{-- Right --}}
        @if($hasRight)

        <div>

            <div class="grid grid-cols-2 gap-4">

                @if(filled($profile?->founded_year))
                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Founded
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        {{ $profile->founded_year }}
                    </div>
                </div>
                @endif

                @if(filled($profile?->total_employees))
                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Employees
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        {{ $profile->total_employees }}
                    </div>
                </div>
                @endif

                @if(filled($profile?->annual_export_revenue))
                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Export Revenue
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        ${{ number_format($profile->annual_export_revenue) }}
                    </div>
                </div>
                @endif

                @if(filled($profile?->registration_capital))
                <div class="rounded-xl border border-gray-200 p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-400">
                        Registration Capital
                    </div>

                    <div class="mt-2 text-lg font-bold text-gray-900">
                        ${{ number_format($profile->registration_capital) }}
                    </div>
                </div>
                @endif

            </div>

        </div>

        @endif

    </div>

</div>

@endif


{{-- ================= CERTIFICATES BLOCK ================= --}}
@php
    $certificates = $company->certificatesMedia()->get();
@endphp
@if($certificates->isNotEmpty())
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

                
            </div>

           

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

            <div class="swiper-slide w-[190px] shrink-0">

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

                        <div class="min-w-0 flex-1 truncate text-sm font-semibold text-gray-900">
    {{ $certificateName ?: $certificate->original_file_name }}
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

   

</div>

    </div>

</div>

      

    </div>




@endif





     




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

   new Swiper('.certificates-swiper', {
    slidesPerView: 'auto',
    spaceBetween: 16,

    navigation: {
        nextEl: '.cert-next',
        prevEl: '.cert-prev',
    },
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
    window.factoryGallery = @json($factoryGallery);
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

