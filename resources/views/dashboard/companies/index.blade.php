@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-6xl mx-auto">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between mb-8">

        <div>
            <div class="flex items-center gap-2 mb-2">

                <span class="inline-flex items-center rounded-full
                             bg-gray-100 px-2.5 py-1
                             text-[11px] font-semibold uppercase tracking-wide
                             text-gray-600">
                    Workspace
                </span>

                <span class="text-xs text-gray-400">
                    Company management
                </span>

            </div>

            <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">
                Companies
            </h1>

            <p class="mt-2 text-sm text-gray-500 max-w-2xl">
                Manage your companies, ownership and company access.
            </p>
        </div>

        <a href="{{ route('dashboard.companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 active:scale-[0.98] transition-all duration-150 shadow-sm"> <span class="text-lg leading-none">+</span> <span>Add New Company</span> </a>

    </div>


    {{-- =========================================================
         ALERTS
    ========================================================== --}}
    <div class="mb-6">
        <x-alerts />
    </div>


    @php
        $typeLabel = function ($type) {
            return match($type) {
                'buyer' => 'Buyer Company',
                'supplier' => 'Supplier Company',
                'logistics' => 'Logistics Company',
                default => ucfirst($type),
            };
        };

        $typeShortLabel = function ($type) {
            return match($type) {
                'buyer' => 'Buyer',
                'supplier' => 'Supplier',
                'logistics' => 'Logistics',
                default => ucfirst($type),
            };
        };

        $activeCount = $activeCompanies->count();
        $inactiveCount = $inactiveCompanies->count();
    @endphp


    {{-- =========================================================
         SUMMARY
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

        {{-- ACTIVE COUNT --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Active companies
                    </p>

                    <p class="mt-2 text-2xl font-semibold tracking-tight text-gray-900">
                        {{ $activeCount }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Available in your workspace
                    </p>
                </div>

                <div class="w-10 h-10 rounded-lg
                            bg-emerald-50 border border-emerald-100
                            flex items-center justify-center">

                    <svg class="w-5 h-5 text-emerald-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M5 12l4 4L19 6"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- INACTIVE COUNT --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-5">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Inactive companies
                    </p>

                    <p class="mt-2 text-2xl font-semibold tracking-tight text-gray-900">
                        {{ $inactiveCount }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Blocked, inactive or deleted
                    </p>
                </div>

                <div class="w-10 h-10 rounded-lg
                            bg-gray-100 border border-gray-200
                            flex items-center justify-center">

                    <svg class="w-5 h-5 text-gray-500"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M18 12H6"/>
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ACTIVE COMPANIES
    ========================================================== --}}
    <section>

        <div class="flex items-end justify-between gap-4 mb-3">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Active Companies
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    Companies currently available for work.
                </p>
            </div>

            <span class="hidden sm:inline-flex items-center rounded-full
                         bg-gray-100 px-2.5 py-1
                         text-xs font-medium text-gray-600">
                {{ $activeCount }} {{ Str::plural('company', $activeCount) }}
            </span>

        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            @if($activeCompanies->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-200">

                            <tr>

                                <th class="px-5 py-3.5 text-left
                                           text-xs font-semibold uppercase tracking-wide
                                           text-gray-500">
                                    Company
                                </th>

                                <th class="px-5 py-3.5 text-left
                                           text-xs font-semibold uppercase tracking-wide
                                           text-gray-500">
                                    Type
                                </th>

                                <th class="px-5 py-3.5 text-left
                                           text-xs font-semibold uppercase tracking-wide
                                           text-gray-500">
                                    Status
                                </th>

                                <th class="px-5 py-3.5 text-right
                                           text-xs font-semibold uppercase tracking-wide
                                           text-gray-500">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach($activeCompanies as $company)

                                @php
                                    $statusClass = match($company->status) {
                                        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'pending' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        default => 'bg-gray-100 text-gray-600 border-gray-200',
                                    };
                                @endphp

                                <tr class="group hover:bg-gray-50/70 transition-colors">

                                    {{-- COMPANY --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="w-10 h-10 shrink-0 rounded-lg
                                                        bg-gray-50 border border-gray-200
                                                        flex items-center justify-center">

@php
    $companyLogo = $company->logo();
@endphp

@if($companyLogo)
    <img
        src="{{ asset($companyLogo->cdn_url) }}"
        alt="{{ $company->name }}"
        class="w-full h-full object-cover rounded-lg
"
    >
@else
   <span class="text-sm font-semibold text-gray-600">
        {{ strtoupper(substr($company->name ?? 'C', 0, 1)) }}
    </span>
@endif


                                            </div>

                                            <div class="min-w-0">

                                                <div class="font-medium text-gray-900 truncate">
                                                    {{ $company->name }}
                                                </div>

                                                @if($company->slug)
                                                    <div class="mt-0.5 text-xs text-gray-400 truncate">
                                                        {{ $company->slug }}
                                                    </div>
                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- TYPE --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex items-center
                                                     rounded-full
                                                     border border-gray-200
                                                     bg-gray-50
                                                     px-2.5 py-1
                                                     text-xs font-medium
                                                     text-gray-600">
                                            {{ $typeShortLabel($company->type) }}
                                        </span>

                                    </td>


                                    {{-- STATUS --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full border
                                                     px-2.5 py-1
                                                     text-xs font-medium
                                                     {{ $statusClass }}">

                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                            {{ ucfirst($company->status) }}

                                        </span>

                                    </td>


                                    {{-- ACTIONS --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center justify-end gap-1">

                                            {{-- TRANSFER OWNER --}}
                                            <button
                                                type="button"
                                                onclick="openOwnerDrawer({{ $company->id }}, @js($company->name))"
                                                class="inline-flex items-center gap-1.5
                                                       px-2.5 py-1.5
                                                       text-xs font-medium
                                                       text-gray-600
                                                       rounded-md
                                                       hover:bg-gray-100
                                                       hover:text-gray-900
                                                       transition">

                                                <svg class="w-3.5 h-3.5"
                                                     fill="none"
                                                     viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="1.8"
                                                          d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m6-10a4 4 0 100-8 4 4 0 000 8zm8-3v6m3-3h-6"/>
                                                </svg>

                                                Transfer
                                            </button>


                                            {{-- EDIT --}}
                                            <a href="{{ route('dashboard.companies.edit', $company->id) }}"
                                               class="inline-flex items-center gap-1.5
                                                      px-2.5 py-1.5
                                                      text-xs font-medium
                                                      text-gray-600
                                                      rounded-md
                                                      hover:bg-gray-100
                                                      hover:text-gray-900
                                                      transition">

                                                <svg class="w-3.5 h-3.5"
                                                     fill="none"
                                                     viewBox="0 0 24 24"
                                                     stroke="currentColor">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="1.8"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 8.5-8.5z"/>
                                                </svg>

                                                Edit
                                            </a>


                                            {{-- DELETE --}}
                                            <form
                                                action="{{ route('dashboard.companies.destroy', $company->id) }}"
                                                method="POST"
                                                class="inline delete-company-form"
                                                data-company-name="{{ $company->name }}">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-1.5
                                                           px-2.5 py-1.5
                                                           text-xs font-medium
                                                           text-red-600
                                                           rounded-md
                                                           hover:bg-red-50
                                                           transition">

                                                    <svg class="w-3.5 h-3.5"
                                                         fill="none"
                                                         viewBox="0 0 24 24"
                                                         stroke="currentColor">
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="1.8"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10"/>
                                                    </svg>

                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                {{-- EMPTY STATE --}}
                <div class="px-6 py-14 text-center">

                    <div class="mx-auto w-12 h-12 rounded-xl
                                bg-gray-50 border border-gray-200
                                flex items-center justify-center">

                        <svg class="w-6 h-6 text-gray-400"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M3 7h18M5 7v12a2 2 0 002 2h10a2 2 0 002-2V7M9 11h6"/>
                        </svg>

                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-gray-900">
                        No active companies
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Create a company to start working with your organization.
                    </p>

                    <a href="{{ route('dashboard.companies.create') }}"
                       class="inline-flex items-center gap-2 mt-5
                              px-4 py-2
                              text-sm font-medium
                              text-white
                              bg-gray-900
                              rounded-lg
                              hover:bg-gray-800
                              transition">

                        <svg class="w-4 h-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M12 5v14M5 12h14"/>
                        </svg>

                        Create company

                    </a>

                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
         INACTIVE COMPANIES
    ========================================================== --}}
    <section class="mt-10">

        <div class="flex items-end justify-between gap-4 mb-3">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Inactive Companies
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    Companies that are blocked, inactive or deleted.
                </p>
            </div>

            <span class="hidden sm:inline-flex items-center rounded-full
                         bg-gray-100 px-2.5 py-1
                         text-xs font-medium text-gray-600">
                {{ $inactiveCount }} {{ Str::plural('company', $inactiveCount) }}
            </span>

        </div>


        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            @if($inactiveCompanies->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-200">

                            <tr>

                                <th class="px-5 py-3.5 text-left
                                           text-xs font-semibold uppercase tracking-wide
                                           text-gray-500">
                                    Company
                                </th>

                                <th class="px-5 py-3.5 text-left
                                           text-xs font-semibold uppercase tracking-wide
                                           text-gray-500">
                                    Type
                                </th>

                                <th class="px-5 py-3.5 text-left
                                           text-xs font-semibold uppercase tracking-wide
                                           text-gray-500">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach($inactiveCompanies as $company)

                                @php
                                    $statusClass = match($company->status) {
                                        'blocked' => 'bg-red-50 text-red-700 border-red-100',
                                        'inactive' => 'bg-orange-50 text-orange-700 border-orange-100',
                                        'deleted' => 'bg-gray-100 text-gray-500 border-gray-200',
                                        default => 'bg-gray-100 text-gray-600 border-gray-200',
                                    };
                                @endphp

                                <tr class="bg-gray-50/50">

                                    {{-- COMPANY --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="w-10 h-10 shrink-0 rounded-lg
                                                        bg-gray-100 border border-gray-200
                                                        flex items-center justify-center">

                                                @php
    $companyLogo = $company->logo();
@endphp

@if($companyLogo)
    <img
        src="{{ asset($companyLogo->cdn_url) }}"
        alt="{{ $company->name }}"
        class="w-full h-full object-cover rounded-lg
"
    >
@else
   <span class="text-sm font-semibold text-gray-600">
        {{ strtoupper(substr($company->name ?? 'C', 0, 1)) }}
    </span>
@endif

                                            </div>

                                            <div class="min-w-0">

                                                <div class="font-medium text-gray-600 truncate">
                                                    {{ $company->name }}
                                                </div>

                                                @if($company->slug)
                                                    <div class="mt-0.5 text-xs text-gray-400 truncate">
                                                        {{ $company->slug }}
                                                    </div>
                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- TYPE --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex items-center
                                                     rounded-full
                                                     border border-gray-200
                                                     bg-gray-100
                                                     px-2.5 py-1
                                                     text-xs font-medium
                                                     text-gray-500">
                                            {{ $typeShortLabel($company->type) }}
                                        </span>

                                    </td>


                                    {{-- STATUS --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full border
                                                     px-2.5 py-1
                                                     text-xs font-medium
                                                     {{ $statusClass }}">

                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                            {{ ucfirst($company->status) }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="px-6 py-10 text-center">

                    <p class="text-sm text-gray-500">
                        No inactive companies.
                    </p>

                </div>

            @endif

        </div>

    </section>

</div>


{{-- =============================================================
     OWNER DRAWER
============================================================== --}}

<div id="owner-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm
            hidden z-50 opacity-0 transition-opacity duration-200">
</div>


<div id="owner-drawer"
     class="fixed right-0 top-0 h-full
            w-full sm:w-[460px]
            bg-white shadow-2xl
            translate-x-full
            transition-transform duration-300
            z-50
            flex flex-col">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b border-gray-200 bg-white">

        <div class="flex items-start justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 mb-2">

                    <span class="inline-flex items-center rounded-full
                                 bg-gray-100 px-2 py-1
                                 text-[11px] font-semibold uppercase tracking-wide
                                 text-gray-600">
                        Ownership
                    </span>

                </div>

                <h3 class="text-lg font-semibold text-gray-900">
                    Transfer ownership
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Change the primary owner of this company.
                </p>

            </div>

            <button
                type="button"
                onclick="closeOwnerDrawer()"
                class="w-8 h-8 shrink-0
                       rounded-lg
                       text-gray-400
                       hover:text-gray-700
                       hover:bg-gray-100
                       transition
                       flex items-center justify-center">

                <svg class="w-5 h-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M6 6l12 12M18 6L6 18"/>
                </svg>

            </button>

        </div>

    </div>


    {{-- FORM --}}
    <form id="owner-form"
          method="POST"
          class="flex flex-col flex-1">

        @csrf

        {{-- CONTENT --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">

            <input type="hidden"
                   name="user_id"
                   id="owner-user-id">


            {{-- COMPANY --}}
            <div id="owner-company-box"
                 class="hidden rounded-xl
                        border border-gray-200
                        bg-gray-50
                        px-4 py-3">

                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                    Company
                </div>

                <div id="owner-company-name"
                     class="mt-1 text-sm font-semibold text-gray-800">
                </div>

            </div>


            {{-- INFO BLOCK --}}
            <div class="rounded-xl
                        bg-amber-50
                        border border-amber-100
                        p-4">

                <div class="flex items-start gap-3">

                    <div class="w-8 h-8 shrink-0 rounded-lg
                                bg-white border border-amber-100
                                flex items-center justify-center">

                        <svg class="w-4 h-4 text-amber-600"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M12 9v4m0 4h.01M10.3 3.8L2.8 17a2 2 0 001.7 3h15a2 2 0 001.7-3L13.7 3.8a2 2 0 00-3.4 0z"/>
                        </svg>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-amber-900">
                            Before you transfer ownership
                        </p>

                        <ul class="mt-2
                                   list-disc
                                   pl-4
                                   space-y-1
                                   text-xs
                                   leading-5
                                   text-amber-800">

                            <li>
                                The selected user becomes the <b>primary owner</b>.
                            </li>

                            <li>
                                Your current role may change according to company rules.
                            </li>

                            <li>
                                The new owner will receive full ownership permissions.
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


            {{-- EMAIL --}}
            <div>

                <label for="owner-email"
                       class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    New owner email
                </label>

                <div class="relative mt-2">

                    <input
                        type="email"
                        name="email"
                        id="owner-email"
                        class="w-full
                               border border-gray-200
                               rounded-lg
                               px-3.5 py-2.5
                               pr-10
                               text-sm
                               text-gray-900
                               placeholder:text-gray-400
                               focus:outline-none
                               focus:border-gray-400
                               focus:ring-2
                               focus:ring-gray-900/10
                               transition"
                        placeholder="example@email.com"
                        autocomplete="off"
                        required>

                    <div id="owner-email-spinner"
                         class="hidden absolute right-3 top-1/2 -translate-y-1/2">

                        <svg class="w-4 h-4 animate-spin text-gray-400"
                             fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="3">
                            </circle>

                            <path class="opacity-75"
                                  fill="currentColor"
                                  d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z">
                            </path>
                        </svg>

                    </div>

                </div>

                <p id="owner-email-result"
                   class="text-xs mt-2 min-h-[18px]">
                </p>

            </div>

        </div>


        {{-- FOOTER --}}
        <div class="border-t border-gray-200
                    bg-white
                    px-6 py-4
                    flex items-center justify-between gap-3">

            <button
                type="button"
                onclick="closeOwnerDrawer()"
                class="px-4 py-2
                       text-sm font-medium
                       rounded-lg
                       border border-gray-200
                       text-gray-600
                       hover:bg-gray-50
                       hover:text-gray-900
                       transition">
                Cancel
            </button>

            <button
                type="submit"
                id="owner-confirm-transfer"
                class="inline-flex items-center justify-center
                       px-4 py-2
                       text-sm font-medium
                       rounded-lg
                       bg-gray-900
                       text-white
                       hover:bg-gray-800
                       transition
                       shadow-sm
                       disabled:opacity-40
                       disabled:cursor-not-allowed"
                disabled>

                Transfer ownership

            </button>

        </div>

    </form>

</div>


{{-- =============================================================
     DELETE CONFIRM MODAL
============================================================== --}}

<div id="delete-overlay"
     class="fixed inset-0
            bg-black/40 backdrop-blur-sm
            hidden
            z-[60]
            opacity-0
            transition-opacity duration-200">
</div>


<div id="delete-modal"
     class="fixed inset-0
            hidden
            z-[61]
            items-center justify-center
            px-4">

    <div class="w-full max-w-md
                rounded-xl
                bg-white
                border border-gray-200
                shadow-2xl
                transform scale-95
                opacity-0
                transition-all duration-200"
         id="delete-modal-card">

        <div class="p-6">

            <div class="flex items-start gap-4">

                <div class="w-10 h-10 shrink-0
                            rounded-lg
                            bg-red-50
                            border border-red-100
                            flex items-center justify-center">

                    <svg class="w-5 h-5 text-red-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M12 9v4m0 4h.01M10.3 3.8L2.8 17a2 2 0 001.7 3h15a2 2 0 001.7 3L13.7 3.8a2 2 0 00-3.4 0z"/>
                    </svg>

                </div>

                <div class="min-w-0">

                    <h3 class="text-base font-semibold text-gray-900">
                        Delete company?
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 leading-5">
                        You are about to delete
                        <span id="delete-company-name"
                              class="font-medium text-gray-700">
                        </span>.
                        This action cannot be undone.
                    </p>

                </div>

            </div>

        </div>


        <div class="px-6 py-4
                    border-t border-gray-200
                    bg-gray-50
                    flex items-center justify-end gap-2">

            <button
                type="button"
                onclick="closeDeleteModal()"
                class="px-4 py-2
                       text-sm font-medium
                       rounded-lg
                       border border-gray-200
                       bg-white
                       text-gray-600
                       hover:bg-gray-50
                       hover:text-gray-900
                       transition">
                Cancel
            </button>

            <button
                type="button"
                id="delete-confirm-button"
                class="px-4 py-2
                       text-sm font-medium
                       rounded-lg
                       bg-red-600
                       text-white
                       hover:bg-red-700
                       transition
                       shadow-sm">
                Delete company
            </button>

        </div>

    </div>

</div>


{{-- =============================================================
     SCRIPT
============================================================== --}}

<script>
    (() => {

        let selectedUserId = null;
        let selectedDeleteForm = null;

        const overlay = document.getElementById('owner-overlay');
        const drawer = document.getElementById('owner-drawer');
        const ownerForm = document.getElementById('owner-form');
        const ownerEmail = document.getElementById('owner-email');
        const ownerEmailResult = document.getElementById('owner-email-result');
        const ownerUserId = document.getElementById('owner-user-id');
        const confirmButton = document.getElementById('owner-confirm-transfer');
        const spinner = document.getElementById('owner-email-spinner');

        const deleteOverlay = document.getElementById('delete-overlay');
        const deleteModal = document.getElementById('delete-modal');
        const deleteModalCard = document.getElementById('delete-modal-card');
        const deleteCompanyName = document.getElementById('delete-company-name');
        const deleteConfirmButton = document.getElementById('delete-confirm-button');


        /*
        |--------------------------------------------------------------------------
        | OWNER DRAWER
        |--------------------------------------------------------------------------
        */

        window.openOwnerDrawer = function (companyId, companyName = '') {

            selectedUserId = null;

            ownerForm.action =
                `/dashboard/companies/${companyId}/transfer-owner`;

            ownerUserId.value = '';

            ownerEmail.value = '';

            ownerEmailResult.innerHTML = '';

            confirmButton.disabled = true;

            const companyBox =
                document.getElementById('owner-company-box');

            const companyNameBox =
                document.getElementById('owner-company-name');

            if (companyName) {
                companyBox.classList.remove('hidden');
                companyNameBox.textContent = companyName;
            } else {
                companyBox.classList.add('hidden');
                companyNameBox.textContent = '';
            }

            overlay.classList.remove('hidden');

            requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                drawer.classList.remove('translate-x-full');
            });

            setTimeout(() => {
                ownerEmail.focus();
            }, 300);
        };


        window.closeOwnerDrawer = function () {

            overlay.classList.add('opacity-0');
            drawer.classList.add('translate-x-full');

            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 250);
        };


        overlay.addEventListener('click', closeOwnerDrawer);


        /*
        |--------------------------------------------------------------------------
        | OWNER EMAIL LOOKUP
        |--------------------------------------------------------------------------
        */

        let lookupTimer = null;
        let lookupRequestId = 0;

        ownerEmail.addEventListener('input', function () {

            const email = this.value.trim();

            selectedUserId = null;
            ownerUserId.value = '';

            confirmButton.disabled = true;

            clearTimeout(lookupTimer);

            if (!email) {
                ownerEmailResult.innerHTML = '';
                spinner.classList.add('hidden');
                return;
            }

            if (!email.includes('@')) {
                ownerEmailResult.innerHTML =
                    '<span class="text-gray-400">Enter a valid email address.</span>';

                spinner.classList.add('hidden');

                return;
            }

            lookupTimer = setTimeout(async () => {

                const requestId = ++lookupRequestId;

                spinner.classList.remove('hidden');

                ownerEmailResult.innerHTML =
                    '<span class="text-gray-400">Checking account...</span>';

                try {

                    const response = await fetch(
                        `/dashboard/users/find-by-email?email=${encodeURIComponent(email)}`,
                        {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Lookup failed');
                    }

                    const data = await response.json();

                    if (requestId !== lookupRequestId) {
                        return;
                    }

                    if (!data.found) {

                        selectedUserId = null;
                        ownerUserId.value = '';

                        confirmButton.disabled = true;

                        ownerEmailResult.innerHTML =
                            '<span class="text-red-600">Account holder not found.</span>';

                        return;
                    }

                    selectedUserId = data.user.id;
                    ownerUserId.value = selectedUserId;

                    confirmButton.disabled = false;

                    const fullName =
                        data.user.full_name ??
                        (
                            data.user.name +
                            ' ' +
                            (data.user.last_name ?? '')
                        );

                    ownerEmailResult.innerHTML = `
                        <span class="text-emerald-600 font-medium">
                            ${escapeHtml(fullName.trim())}
                        </span>
                        <span class="text-gray-400">
                            (${escapeHtml(data.user.email)})
                        </span>
                    `;

                } catch (error) {

                    selectedUserId = null;
                    ownerUserId.value = '';

                    confirmButton.disabled = true;

                    ownerEmailResult.innerHTML =
                        '<span class="text-red-600">Unable to verify this account. Please try again.</span>';

                } finally {

                    if (requestId === lookupRequestId) {
                        spinner.classList.add('hidden');
                    }

                }

            }, 350);

        });


        /*
        |--------------------------------------------------------------------------
        | OWNER FORM SUBMIT
        |--------------------------------------------------------------------------
        */

        ownerForm.addEventListener('submit', function (event) {

            if (!selectedUserId) {
                event.preventDefault();

                ownerEmailResult.innerHTML =
                    '<span class="text-red-600">Please enter a valid account email.</span>';

                ownerEmail.focus();

                return;
            }

            ownerUserId.value = selectedUserId;

            confirmButton.disabled = true;
            confirmButton.textContent = 'Transferring...';
        });


        /*
        |--------------------------------------------------------------------------
        | DELETE MODAL
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('.delete-company-form')
            .forEach(form => {

                form.addEventListener('submit', function (event) {

                    event.preventDefault();

                    selectedDeleteForm = this;

                    const companyName =
                        this.dataset.companyName || 'this company';

                    deleteCompanyName.textContent = companyName;

                    deleteOverlay.classList.remove('hidden');
                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');

                    requestAnimationFrame(() => {

                        deleteOverlay.classList.remove('opacity-0');

                        deleteModalCard.classList.remove(
                            'scale-95',
                            'opacity-0'
                        );

                        deleteModalCard.classList.add(
                            'scale-100',
                            'opacity-100'
                        );

                    });

                });

            });


        window.closeDeleteModal = function () {

            deleteOverlay.classList.add('opacity-0');

            deleteModalCard.classList.remove(
                'scale-100',
                'opacity-100'
            );

            deleteModalCard.classList.add(
                'scale-95',
                'opacity-0'
            );

            setTimeout(() => {

                deleteOverlay.classList.add('hidden');

                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');

                selectedDeleteForm = null;

            }, 200);
        };


        deleteOverlay.addEventListener(
            'click',
            closeDeleteModal
        );


        deleteConfirmButton.addEventListener(
            'click',
            function () {

                if (!selectedDeleteForm) {
                    return;
                }

                this.disabled = true;
                this.textContent = 'Deleting...';

                selectedDeleteForm.submit();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ESCAPE KEY
        |--------------------------------------------------------------------------
        */

        document.addEventListener('keydown', function (event) {

            if (event.key !== 'Escape') {
                return;
            }

            if (!drawer.classList.contains('translate-x-full')) {
                closeOwnerDrawer();
                return;
            }

            if (!deleteModal.classList.contains('hidden')) {
                closeDeleteModal();
            }

        });


        /*
        |--------------------------------------------------------------------------
        | HTML ESCAPE
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

        }

    })();
</script>

@endsection