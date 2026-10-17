@extends('dashboard.layout')

@section('dashboard-content')
@php
    $selectedMethodIds = collect($selectedMethodIds ?? [])->map(fn ($id) => (int) $id)->all();
    $selectedPaymentTermId = isset($selectedPaymentTermId) ? (int) $selectedPaymentTermId : null;

    $selectedMethods = $paymentMethods->filter(
        fn ($method) => in_array((int) $method->id, $selectedMethodIds, true)
    );

    $selectedTerm = $paymentTerms->first(
        fn ($term) => (int) $term->id === $selectedPaymentTermId
    );
@endphp

<div class="mx-auto max-w-5xl space-y-6">
    <x-alerts />

    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Payments</h1>
        <p class="mt-1 text-sm text-slate-500">
            Set your default payment methods and terms. They will be preselected when creating products.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">Please check the following:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        {{-- Payment Methods --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="M3 10h18M7 15h3"></path>
                            </svg>
                        </span>
                        <h2 class="text-base font-semibold text-slate-900">Payment Methods</h2>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">
                        Default methods for your products.
                    </p>
                </div>

                <button
                    type="button"
                    data-open-drawer="methods"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Add
                </button>
            </div>

            <div class="mt-4 space-y-2">
    @forelse ($selectedMethods as $method)
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3">
            <div class="flex h-10 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-white p-2">
                @if (!empty($method->icon_svg))
                    <div class="flex h-full w-full items-center justify-center [&>svg]:max-h-full [&>svg]:max-w-full [&>svg]:h-auto [&>svg]:w-auto">
                        {!! $method->icon_svg !!}
                    </div>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6 text-gray-400"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path stroke-linecap="round" d="M3 10h18"/>
                    </svg>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <div class="text-sm font-semibold text-emerald-800">
                    {{ $method->name }}
                </div>

                @if (!empty($method->description))
                    <div class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-gray-500">
                        {{ $method->description }}
                    </div>
                @endif
            </div>

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 shrink-0 text-emerald-600"
                 viewBox="0 0 20 20"
                 fill="currentColor">
                <path fill-rule="evenodd"
                      d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 011.415-1.415l2.793 2.793 6.492-6.493a1 1 0 011.415 0z"
                      clip-rule="evenodd"/>
            </svg>
        </div>
    @empty
        <p class="rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
            No payment methods selected yet.
        </p>
    @endforelse
</div>

            <div class="mt-3 border-t border-slate-100 pt-3">
                <p class="text-xs text-slate-400">
                    {{ count($selectedMethodIds) }} selected
                </p>
            </div>
        </section>

        {{-- Payment Terms --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 8h8M8 12h8M8 16h4"></path>
                            </svg>
                        </span>
                        <h2 class="text-base font-semibold text-slate-900">Payment Terms</h2>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">
                        Your default payment term for new products.
                    </p>
                </div>

                <button
                    type="button"
                    data-open-drawer="terms"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Add
                </button>
            </div>

            <div class="mt-4 space-y-2">
    @if ($selectedTerm)
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-emerald-100 bg-white text-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7h12M8 12h12M8 17h12"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m3 7 1 1 2-2M3 12l1 1 2-2M3 17l1 1 2-2"/>
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <div class="text-sm font-semibold text-emerald-800">
                    {{ $selectedTerm->name }}
                </div>

                @if (!empty($selectedTerm->description))
                    <div class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-gray-500">
                        {{ $selectedTerm->description }}
                    </div>
                @endif
            </div>

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 shrink-0 text-emerald-600"
                 viewBox="0 0 20 20"
                 fill="currentColor">
                <path fill-rule="evenodd"
                      d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 011.415-1.415l2.793 2.793 6.492-6.493a1 1 0 011.415 0z"
                      clip-rule="evenodd"/>
            </svg>
        </div>
    @else
        <p class="rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
            No payment terms selected yet.
        </p>
    @endif
</div>

            <div class="mt-3 border-t border-slate-100 pt-3">
                <p class="text-xs text-slate-400">
                    {{ $selectedTerm ? '1 selected' : 'None selected' }}
                </p>
            </div>
        </section>
    </div>
</div>

{{-- Drawer backdrop --}}
<div
    id="payments-drawer-backdrop"
    class="fixed inset-0 z-40 hidden bg-slate-900/40 opacity-0 transition-opacity duration-200"
    aria-hidden="true"
></div>

{{-- Payment Methods Drawer --}}
<aside
    id="payments-methods-drawer"
    class="fixed right-0 top-0 z-50 flex h-full w-full max-w-[460px] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
    role="dialog"
    aria-modal="true"
    aria-labelledby="payments-methods-title"
    aria-hidden="true"
    data-drawer="methods"
>
    <form
        id="payments-methods-form"
        action="{{ route('supplier.payments.methods.update') }}"
        method="POST"
        class="flex min-h-0 flex-1 flex-col"
    >
        @csrf
        @method('PUT')

        {{-- Header --}}
        <div class="border-b bg-gray-50 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="payments-methods-title" class="text-lg font-semibold text-gray-900">
                        Default Payment Methods
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Select all methods you want to use by default.
                    </p>
                </div>

                <button
                    type="button"
                    data-close-drawer
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Close"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m18 6-12 12M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Search --}}
        <div class="border-b border-gray-200 px-6 py-4">
            <label for="payment-method-search" class="sr-only">Search payment methods</label>

            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m16 16 4 4"/>
                </svg>

                <input
                    id="payment-method-search"
                    type="search"
                    placeholder="Search payment methods..."
                    class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                >
            </div>

            <p class="mt-3 text-xs text-gray-400">
                <span id="payment-method-count">{{ count($selectedMethodIds) }}</span> selected
            </p>
        </div>

        {{-- Payment Methods List --}}
        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5">
            <div class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-400">
                Payment Methods
            </div>

            <div id="payment-method-options" class="space-y-3">
                @forelse ($paymentMethods as $method)
                    @php
                        $methodIsSelected = in_array(
                            (int) $method->id,
                            $selectedMethodIds,
                            true
                        );
                    @endphp

                    <button
                        type="button"
                        data-option
                        data-option-id="{{ $method->id }}"
                        data-option-label="{{ $method->name }}"
                        data-option-search="{{ \Illuminate\Support\Str::lower($method->name . ' ' . ($method->description ?? '')) }}"
                        aria-pressed="{{ $methodIsSelected ? 'true' : 'false' }}"
                        class="payment-option flex w-full items-center gap-3 rounded-xl border p-3 text-left transition focus:outline-none focus:ring-2 focus:ring-gray-200 {{ $methodIsSelected ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}"
                    >
                        {{-- Payment Method Icon --}}
                        <div class="flex h-12 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-white p-2">
                            @if (!empty($method->icon_svg))
                                <div class="flex h-full w-full items-center justify-center [&>svg]:max-h-full [&>svg]:max-w-full [&>svg]:h-auto [&>svg]:w-auto">
                                    {!! $method->icon_svg !!}
                                </div>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-6 w-6 text-gray-400"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.7">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path stroke-linecap="round" d="M3 10h18"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Method Name --}}
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold {{ $methodIsSelected ? 'text-emerald-800' : 'text-gray-900' }}">
                                {{ $method->name }}
                            </div>

                            @if (!empty($method->description))
                                <div class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-gray-500">
                                    {{ $method->description }}
                                </div>
                            @endif
                        </div>

                        {{-- Selection Indicator --}}
                        <div
                            data-selection-indicator
                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition {{ $methodIsSelected ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300 bg-white text-transparent' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-3 w-3"
                                 viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 011.415-1.415l2.793 2.793 6.492-6.493a1 1 0 011.415 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </button>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 px-4 py-10 text-center">
                        <p class="text-sm font-medium text-gray-700">
                            No payment methods available
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Available payment methods will appear here.
                        </p>
                    </div>
                @endforelse
            </div>

            <p id="payment-method-no-results" class="hidden py-8 text-center text-sm text-gray-400">
                No matching payment methods found.
            </p>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between gap-4 border-t bg-white px-6 py-4">
            <button
                type="button"
                data-close-drawer
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-50"
            >
                Cancel
            </button>

            <div class="flex items-center gap-3">
                <div class="whitespace-nowrap text-xs text-gray-400">
                    Save your default settings
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white shadow-sm transition hover:bg-gray-800"
                >
                    Save
                </button>
            </div>
        </div>
    </form>
</aside>


{{-- Payment Terms Drawer --}}
<aside
    id="payments-terms-drawer"
    class="fixed right-0 top-0 z-50 flex h-full w-full max-w-[460px] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
    role="dialog"
    aria-modal="true"
    aria-labelledby="payments-terms-title"
    aria-hidden="true"
    data-drawer="terms"
>
    <form
        id="payments-terms-form"
        action="{{ route('supplier.payments.term.update') }}"
        method="POST"
        class="flex min-h-0 flex-1 flex-col"
    >
        @csrf
        @method('PUT')

        {{-- Header --}}
        <div class="border-b bg-gray-50 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="payments-terms-title" class="text-lg font-semibold text-gray-900">
                        Default Payment Term
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Select one term to use by default.
                    </p>
                </div>

                <button
                    type="button"
                    data-close-drawer
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Close"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m18 6-12 12M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Search --}}
        <div class="border-b border-gray-200 px-6 py-4">
            <label for="payment-term-search" class="sr-only">Search payment terms</label>

            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m16 16 4 4"/>
                </svg>

                <input
                    id="payment-term-search"
                    type="search"
                    placeholder="Search payment terms..."
                    class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                >
            </div>

            <p class="mt-3 text-xs text-gray-400">
                Choose one option
            </p>
        </div>

        {{-- Payment Terms List --}}
        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5">
            <div class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-400">
                Payment Terms
            </div>

            <div id="payment-term-options" class="space-y-3">
                @forelse ($paymentTerms as $term)
                    @php
                        $termIsSelected = (int) $selectedPaymentTermId === (int) $term->id;
                    @endphp

                    <button
                        type="button"
                        data-option
                        data-option-id="{{ $term->id }}"
                        data-option-label="{{ $term->name }}"
                        data-option-search="{{ \Illuminate\Support\Str::lower($term->name . ' ' . ($term->description ?? '')) }}"
                        aria-pressed="{{ $termIsSelected ? 'true' : 'false' }}"
                        class="payment-option flex w-full items-center gap-3 rounded-xl border p-3 text-left transition focus:outline-none focus:ring-2 focus:ring-gray-200 {{ $termIsSelected ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}"
                    >
                        {{-- Term Icon --}}
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-100 bg-white text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 12h12M8 17h12"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 1 1 2-2M3 12l1 1 2-2M3 17l1 1 2-2"/>
                            </svg>
                        </div>

                        {{-- Term Name --}}
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold {{ $termIsSelected ? 'text-emerald-800' : 'text-gray-900' }}">
                                {{ $term->name }}
                            </div>

                            @if (!empty($term->description))
                                <div class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-gray-500">
                                    {{ $term->description }}
                                </div>
                            @endif
                        </div>

                        {{-- Selection Indicator --}}
                        <div
                            data-selection-indicator
                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition {{ $termIsSelected ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300 bg-white text-transparent' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-3 w-3"
                                 viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M16.704 5.29a1 1 0 010 1.415l-7.2 7.2a1 1 0 01-1.415 0l-3.5-3.5a1 1 0 011.415-1.415l2.793 2.793 6.492-6.493a1 1 0 011.415 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </button>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 px-4 py-10 text-center">
                        <p class="text-sm font-medium text-gray-700">
                            No payment terms available
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Available payment terms will appear here.
                        </p>
                    </div>
                @endforelse
            </div>

            <p id="payment-term-no-results" class="hidden py-8 text-center text-sm text-gray-400">
                No matching payment terms found.
            </p>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between gap-4 border-t bg-white px-6 py-4">
            <button
                type="button"
                data-close-drawer
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-50"
            >
                Cancel
            </button>

            <div class="flex items-center gap-3">
                <div class="whitespace-nowrap text-xs text-gray-400">
                    Save your default settings
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white shadow-sm transition hover:bg-gray-800"
                >
                    Save
                </button>
            </div>
        </div>
    </form>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const backdrop = document.getElementById('payments-drawer-backdrop');
    const drawers = {
        methods: document.getElementById('payments-methods-drawer'),
        terms: document.getElementById('payments-terms-drawer'),
    };

    let activeDrawer = null;
    let lastFocusedElement = null;

    const selectedMethodIds = new Set(
        @json(array_values(array_map('intval', $selectedMethodIds)))
    );

    let selectedTermId = @json($selectedPaymentTermId);

    const classes = {
        selected: ['border-emerald-300', 'bg-emerald-50', 'text-emerald-800'],
        unselected: ['border-slate-200', 'bg-white', 'text-slate-600'],
    };

    function updateChip(button, selected) {
    button.setAttribute('aria-pressed', selected ? 'true' : 'false');

    if (
    button.closest('#payment-method-options') ||
    button.closest('#payment-term-options')
) {
        button.classList.toggle('border-emerald-200', selected);
        button.classList.toggle('bg-emerald-50', selected);
        button.classList.toggle('border-gray-200', !selected);
        button.classList.toggle('bg-white', !selected);
        button.classList.toggle('hover:border-gray-300', !selected);
        button.classList.toggle('hover:bg-gray-50', !selected);

        const name = button.querySelector('.text-sm.font-semibold');

        if (name) {
            name.classList.toggle('text-emerald-800', selected);
            name.classList.toggle('text-gray-900', !selected);
        }

        const indicator = button.querySelector('[data-selection-indicator]');

        if (indicator) {
            indicator.classList.toggle('border-emerald-600', selected);
            indicator.classList.toggle('bg-emerald-600', selected);
            indicator.classList.toggle('text-white', selected);
            indicator.classList.toggle('border-gray-300', !selected);
            indicator.classList.toggle('bg-white', !selected);
            indicator.classList.toggle('text-transparent', !selected);
        }

        return;
    }

    classes.selected.forEach(className => {
        button.classList.toggle(className, selected);
    });

    classes.unselected.forEach(className => {
        button.classList.toggle(className, !selected);
    });

    const check = button.querySelector('[data-check]');

    if (check) {
        check.classList.toggle('hidden', !selected);
    }
}

    function updateMethodCount() {
        const count = document.getElementById('payment-method-count');
        if (count) count.textContent = selectedMethodIds.size;
    }

    function openDrawer(type, trigger) {
        if (!drawers[type]) return;

        lastFocusedElement = trigger || document.activeElement;
        activeDrawer = type;

        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            drawers[type].classList.remove('translate-x-full');
            drawers[type].setAttribute('aria-hidden', 'false');

            const search = drawers[type].querySelector('input[type="search"]');
            if (search) search.focus();
        });
    }

    function closeDrawer() {
        if (!activeDrawer) return;

        const drawer = drawers[activeDrawer];
        drawer.classList.add('translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        backdrop.classList.add('opacity-0');

        const previousFocus = lastFocusedElement;
        activeDrawer = null;

        window.setTimeout(() => {
            if (!activeDrawer) {
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                if (previousFocus && typeof previousFocus.focus === 'function') {
                    previousFocus.focus();
                }
            }
        }, 300);
    }

    document.querySelectorAll('[data-open-drawer]').forEach(button => {
        button.addEventListener('click', () => {
            openDrawer(button.dataset.openDrawer, button);
        });
    });

    document.querySelectorAll('[data-close-drawer]').forEach(button => {
        button.addEventListener('click', closeDrawer);
    });

    backdrop.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeDrawer();
    });

    // Initialize chips from values saved by the server.
    drawers.methods.querySelectorAll('[data-option]').forEach(button => {
        updateChip(button, selectedMethodIds.has(Number(button.dataset.optionId)));
    });

    drawers.terms.querySelectorAll('[data-option]').forEach(button => {
        updateChip(button, selectedTermId !== null && Number(button.dataset.optionId) === Number(selectedTermId));
    });

    updateMethodCount();

    // Toggle payment methods: multiple selections are allowed.
    drawers.methods.querySelectorAll('[data-option]').forEach(button => {
        button.addEventListener('click', () => {
            const id = Number(button.dataset.optionId);

            if (selectedMethodIds.has(id)) {
                selectedMethodIds.delete(id);
                updateChip(button, false);
            } else {
                selectedMethodIds.add(id);
                updateChip(button, true);
            }

            updateMethodCount();
        });
    });

    // Payment terms: exactly one default term can be selected.
drawers.terms.querySelectorAll('[data-option]').forEach(button => {
    button.addEventListener('click', () => {
        const id = Number(button.dataset.optionId);

        // Clicking the selected term again clears the selection.
        selectedTermId = selectedTermId === id ? null : id;

        drawers.terms.querySelectorAll('[data-option]').forEach(option => {
            const isSelected =
                selectedTermId !== null &&
                Number(option.dataset.optionId) === selectedTermId;

            updateChip(option, isSelected);
        });
    });
});

    // Search filters the available chips inside each drawer.
    function setupSearch(inputId, optionsId, emptyId) {
        const input = document.getElementById(inputId);
        const options = document.getElementById(optionsId);
        const empty = document.getElementById(emptyId);

        if (!input || !options || !empty) return;

        input.addEventListener('input', () => {
            const query = input.value.trim().toLocaleLowerCase();
            let visibleCount = 0;

            options.querySelectorAll('[data-option]').forEach(button => {
                const haystack = (button.dataset.optionSearch || button.dataset.optionLabel || '').toLocaleLowerCase();
                const visible = haystack.includes(query);

                button.classList.toggle('hidden', !visible);
                if (visible) visibleCount++;
            });

            empty.classList.toggle('hidden', visibleCount > 0);
        });
    }

    setupSearch('payment-method-search', 'payment-method-options', 'payment-method-no-results');
    setupSearch('payment-term-search', 'payment-term-options', 'payment-term-no-results');

    // Submit only the currently selected IDs.
    document.getElementById('payments-methods-form').addEventListener('submit', function () {
        this.querySelectorAll('[data-generated-input]').forEach(input => input.remove());

        selectedMethodIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'payment_method_ids[]';
            input.value = id;
            input.dataset.generatedInput = 'true';
            this.appendChild(input);
        });
    });

    document.getElementById('payments-terms-form').addEventListener('submit', function () {
        this.querySelectorAll('[data-generated-input]').forEach(input => input.remove());

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'payment_term_id';
        input.value = selectedTermId === null ? '' : selectedTermId;
        input.dataset.generatedInput = 'true';
        this.appendChild(input);
    });
});
</script>
@endsection