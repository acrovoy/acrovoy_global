
@extends('dashboard.layout')

@section('dashboard-content')

<div class="space-y-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Return Policies
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Manage your return policies, choose a default policy and create custom return rules.
            </p>
        </div>

        <a href="{{ route('supplier.return-policies.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800">

            {{-- Plus Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 5v14M5 12h14"/>
            </svg>

            Create Return Policy
        </a>

    </div>


    {{-- =========================================================
        ALERTS
    ========================================================== --}}

    <x-alerts />


    {{-- =========================================================
        RETURN POLICIES TABLE
    ========================================================== --}}

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-[1500px] w-full text-left">

                {{-- =================================================
                    TABLE HEAD
                ================================================== --}}

                <thead class="border-b border-gray-200 bg-gray-50">

                    <tr>

                        <th class="w-[70px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            ID
                        </th>

                        <th class="min-w-[280px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Policy
                        </th>

                        <th class="min-w-[150px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Return Window
                        </th>

                        <th class="min-w-[260px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Reasons
                        </th>

                        <th class="min-w-[220px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Resolutions
                        </th>

                        <th class="min-w-[150px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Shipping
                        </th>

                        <th class="min-w-[150px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Restocking
                        </th>

                        <th class="min-w-[120px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Type
                        </th>

                        <th class="min-w-[110px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Status
                        </th>

                        <th class="min-w-[260px] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- =================================================
                    TABLE BODY
                ================================================== --}}

                <tbody class="divide-y divide-gray-100">

                    @forelse($returnPolicies as $policy)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | POLICY TYPE
                            |--------------------------------------------------------------------------
                            |
                            | Platform policies can be stored either as:
                            |
                            | owner_type = NULL / owner_id = NULL
                            |
                            | or, in the current database:
                            |
                            | owner_type = '' / owner_id = 0
                            |
                            */

                            $isPlatformPolicy =
                                (
                                    $policy->owner_type === null
                                    && $policy->owner_id === null
                                )
                                ||
                                (
                                    $policy->owner_type === ''
                                    && (int) $policy->owner_id === 0
                                );

                            $isSupplierPolicy = !$isPlatformPolicy;

                            $isDefault =
                                (int) $defaultReturnPolicyId === (int) $policy->id;


                            /*
                            |--------------------------------------------------------------------------
                            | TRANSLATION
                            |--------------------------------------------------------------------------
                            */

                            $currentLocale = app()->getLocale();

                            $translation = $policy->translations
                                ->firstWhere('locale', $currentLocale);

                            if (!$translation) {
                                $translation = $policy->translations
                                    ->firstWhere('locale', 'en');
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | DISPLAY VALUES
                            |--------------------------------------------------------------------------
                            */

                            $policyName =
                                $translation?->name
                                ?: $policy->name;

                            $policyDescription =
                                $translation?->description
                                ?: null;


                            /*
                            |--------------------------------------------------------------------------
                            | RETURN WINDOW
                            |--------------------------------------------------------------------------
                            */

                            $returnWindow =
                                $policy->return_window_days !== null
                                    ? $policy->return_window_days . ' days'
                                    : 'No limit';


                            /*
                            |--------------------------------------------------------------------------
                            | SHIPPING
                            |--------------------------------------------------------------------------
                            */

                            $shippingPayer = match ($policy->return_shipping_payer) {
                                'buyer' => 'Buyer',
                                'supplier' => 'Supplier',
                                'depends_on_reason' => 'Depends on reason',
                                default => 'Not specified',
                            };


                            /*
                            |--------------------------------------------------------------------------
                            | RESTOCKING
                            |--------------------------------------------------------------------------
                            */

                            $restocking =
                                $policy->restocking_fee_enabled
                                    ? (
                                        $policy->restocking_fee_percent !== null
                                            ? $policy->restocking_fee_percent . '%'
                                            : 'Enabled'
                                    )
                                    : 'None';

                        @endphp


                        <tr class="align-top transition hover:bg-gray-50/70">

                            {{-- =================================================
                                ID
                            ================================================== --}}

                            <td class="px-5 py-4 text-sm font-medium text-gray-500">
                                #{{ $policy->id }}
                            </td>


                            {{-- =================================================
                                POLICY
                            ================================================== --}}

                            <td class="px-5 py-4">

                                <div class="min-w-0">

                                    <div class="flex items-center gap-2">

                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $policyName }}
                                        </span>

                                        @if($isDefault)

                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                                Default
                                            </span>

                                        @endif

                                    </div>

                                    <div class="mt-1 text-xs font-medium text-gray-400">
                                        {{ $policy->code }}
                                    </div>

                                    @if($policyDescription)

                                        <div class="mt-2 max-w-[360px] text-xs leading-relaxed text-gray-500">
                                            {{ $policyDescription }}
                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- =================================================
                                RETURN WINDOW
                            ================================================== --}}

                            <td class="px-5 py-4">

                                <span class="text-sm text-gray-700">
                                    {{ $returnWindow }}
                                </span>

                            </td>


                            {{-- =================================================
                                REASONS
                            ================================================== --}}

                            <td class="px-5 py-4">

                                @if($policy->reasons->isNotEmpty())

                                    <div class="flex flex-wrap gap-1.5">

                                        @foreach($policy->reasons as $reason)

                                            @php
                                                $reasonTranslation = $reason->translations
                                                    ->firstWhere('locale', $currentLocale);

                                                if (!$reasonTranslation) {
                                                    $reasonTranslation = $reason->translations
                                                        ->firstWhere('locale', 'en');
                                                }

                                                $reasonName =
                                                    $reasonTranslation?->name
                                                    ?: $reason->code;
                                            @endphp

                                            <span class="inline-flex items-center rounded-lg bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-600">
                                                {{ $reasonName }}
                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-sm text-gray-400">
                                        None
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                RESOLUTIONS
                            ================================================== --}}

                            <td class="px-5 py-4">

                                @if($policy->resolutions->isNotEmpty())

                                    <div class="flex flex-wrap gap-1.5">

                                        @foreach($policy->resolutions as $resolution)

                                            @php
                                                $resolutionTranslation = $resolution->translations
                                                    ->firstWhere('locale', $currentLocale);

                                                if (!$resolutionTranslation) {
                                                    $resolutionTranslation = $resolution->translations
                                                        ->firstWhere('locale', 'en');
                                                }

                                                $resolutionName =
                                                    $resolutionTranslation?->name
                                                    ?: $resolution->code;
                                            @endphp

                                            <span class="inline-flex items-center rounded-lg bg-gray-100 px-2 py-1 text-[11px] font-medium text-gray-600">
                                                {{ $resolutionName }}
                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-sm text-gray-400">
                                        None
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                SHIPPING
                            ================================================== --}}

                            <td class="px-5 py-4">

                                <span class="text-sm text-gray-700">
                                    {{ $shippingPayer }}
                                </span>

                            </td>


                            {{-- =================================================
                                RESTOCKING
                            ================================================== --}}

                            <td class="px-5 py-4">

                                <span class="text-sm text-gray-700">
                                    {{ $restocking }}
                                </span>

                            </td>


                            {{-- =================================================
                                TYPE
                            ================================================== --}}

                            <td class="px-5 py-4">

                                @if($isPlatformPolicy)

                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        Platform
                                    </span>

                                @else

                                    <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                        My Policy
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                STATUS
                            ================================================== --}}

                            <td class="px-5 py-4">

                                @if($policy->is_active)

                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">

                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                        Active

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-500">

                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>

                                        Inactive

                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                ACTIONS
                            ================================================== --}}

                            <td class="px-5 py-4">

                                <div class="flex flex-wrap items-center gap-2">

                                    {{-- =================================================
                                        PLATFORM POLICY
                                    ================================================== --}}

                                    @if($isPlatformPolicy)

                                        {{-- Platform policies are read-only.
                                             Supplier can only select an active
                                             platform policy as default. --}}

                                        @if($isDefault)

                                            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                                Default
                                            </span>

                                        @elseif($policy->is_active)

                                            <form method="POST"
                                                  action="{{ route('supplier.return-policies.set-default', $policy) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                                                    Set default
                                                </button>

                                            </form>

                                        @endif


                                    {{-- =================================================
                                        SUPPLIER CUSTOM POLICY
                                    ================================================== --}}

                                    @else

                                        {{-- Set Default --}}

                                        @if($isDefault)

                                            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                                Default
                                            </span>

                                        @else

                                            <form method="POST"
                                                  action="{{ route('supplier.return-policies.set-default', $policy) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                                                    Set default
                                                </button>

                                            </form>

                                        @endif


                                        {{-- Edit --}}

                                        <a href="{{ route('supplier.return-policies.edit', $policy) }}"
                                           class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">

                                            Edit

                                        </a>


                                        {{-- Activate / Deactivate --}}

                                        <form method="POST"
                                              action="{{ route('supplier.return-policies.toggle-active', $policy) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">

                                                {{ $policy->is_active ? 'Deactivate' : 'Activate' }}

                                            </button>

                                        </form>


                                        {{-- Delete --}}

                                        <button type="button"
                                                onclick="openDeleteModal({{ $policy->id }})"
                                                class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">

                                            Delete

                                        </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10"
                                class="px-5 py-14 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">

                                        {{-- Return Icon --}}

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-6 w-6 text-gray-400"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.7">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M9 14l-4-4m0 0l4-4m-4 4h10a5 5 0 015 5v1"/>

                                        </svg>

                                    </div>

                                    <h3 class="mt-4 text-sm font-semibold text-gray-900">
                                        No return policies found
                                    </h3>

                                    <p class="mt-1 max-w-md text-sm text-gray-500">
                                        Create your first custom return policy to define how returns are handled for your products.
                                    </p>

                                    <a href="{{ route('supplier.return-policies.create') }}"
                                       class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800">

                                        Create Return Policy

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =============================================================
    DELETE MODAL
============================================================= --}}

<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

        <div class="flex items-start gap-4">

            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5 text-red-600"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14A2 2 0 003.84 21h16.32a2 2 0 001.73-3.14l-8.18-14a2 2 0 00-3.42 0z"/>

                </svg>

            </div>

            <div>

                <h3 class="text-base font-semibold text-gray-900">
                    Delete Return Policy
                </h3>

                <p class="mt-1 text-sm leading-relaxed text-gray-500">
                    Are you sure you want to delete this return policy? This action cannot be undone.
                </p>

            </div>

        </div>


        <div class="mt-6 flex justify-end gap-3">

            <button type="button"
                    onclick="closeDeleteModal()"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">

                Cancel

            </button>

            <form id="deletePolicyForm"
                  method="POST">

                @csrf
                @method('DELETE')

                <button type="submit"
                        class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">

                    Delete Policy

                </button>

            </form>

        </div>

    </div>

</div>


{{-- =============================================================
    DELETE MODAL SCRIPT
============================================================= --}}

<script>

    function openDeleteModal(policyId)
    {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deletePolicyForm');

        if (!modal || !form) {
            return;
        }

        form.action = "{{ url('/supplier/return-policies') }}/" + policyId;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeDeleteModal()
    {
        const modal = document.getElementById('deleteModal');

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }


    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeDeleteModal();
        }

    });


    document.getElementById('deleteModal')?.addEventListener('click', function (event) {

        if (event.target === this) {
            closeDeleteModal();
        }

    });

</script>

@endsection

