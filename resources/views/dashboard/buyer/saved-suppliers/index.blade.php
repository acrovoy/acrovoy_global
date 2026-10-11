
@extends('dashboard.layout')

@section('dashboard-content')

@php
    /*
     * Resolve the public supplier URL using the existing route table.
     * Adjust the candidate route names if your project uses another name.
     */
    $supplierProfileRoute = collect([
        'supplier.show',
        'supplier.profile.show',
        'suppliers.show',
        'catalog.supplier.show',
    ])->first(fn ($name) => \Illuminate\Support\Facades\Route::has($name));

    $supplierProfileUrl = function ($supplier) use ($supplierProfileRoute) {
        if (!$supplierProfileRoute) {
            return null;
        }

        try {
            return route($supplierProfileRoute, $supplier->slug);
        } catch (\Throwable $e) {
            try {
                return route($supplierProfileRoute, $supplier);
            } catch (\Throwable $e) {
                return null;
            }
        }
    };

    /*
     * Resolve the supplier logo URL from the existing Media model.
     * The Media URL field may differ in this project, so use only
     * common URL fields that may be present.
     */
    $supplierLogoUrl = function ($supplier) {
        $media = $supplier->media
            ->where('collection', 'company_logos')
            ->sortByDesc('id')
            ->first();

        if (!$media) {
            return null;
        }

        foreach (['cdn_url', 'url', 'path'] as $field) {
            $value = data_get($media, $field);

            if (is_string($value) && trim($value) !== '') {
                if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])) {
                    return $value;
                }

                if ($field === 'path') {
                    return \Illuminate\Support\Facades\Storage::url($value);
                }

                return $value;
            }
        }

        return null;
    };
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                Saved Suppliers
            </h1>

            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">
                Keep track of companies you are interested in and return to their
                profiles whenever you need them.
            </p>
        </div>

        @if (\Illuminate\Support\Facades\Route::has('catalog.index'))
            <a
                href="{{ route('catalog.index') }}"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg
                       bg-gray-900 px-4 py-2 text-sm font-medium text-white
                       transition hover:bg-gray-800 focus:outline-none
                       focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 5v14m-7-7h14" />
                </svg>

                Explore Marketplace
            </a>
        @endif
    </div>

    {{-- AJAX feedback --}}
    <div
        id="saved-suppliers-message"
        class="hidden rounded-lg border px-4 py-3 text-sm"
        role="status"
        aria-live="polite"
    ></div>

    {{-- Summary --}}
    <div class="flex flex-col gap-2 border-b border-gray-200 pb-4 sm:flex-row
                sm:items-center sm:justify-between">
        <p class="text-sm text-gray-600">
            <span id="saved-suppliers-count" class="font-semibold text-gray-900">
                {{ $items->total() }}
            </span>
            {{ $items->total() === 1 ? 'saved supplier' : 'saved suppliers' }}
        </p>

        <p class="text-xs text-gray-500">
            Your personal supplier shortlist
        </p>
    </div>

    {{-- Supplier cards --}}
    @if ($items->isNotEmpty())

        <div id="saved-suppliers-list" class="space-y-4">

            @foreach ($items as $item)

                @php
                    $supplier = $item->supplier;

                    if (!$supplier) {
                        continue;
                    }

                    $supplierName = $supplier->name ?: 'Supplier';
                    $description = $supplier->short_description;
                    $countryName = $supplier->country?->name;
                    $logoUrl = $supplierLogoUrl($supplier);
                    $profileUrl = $supplierProfileUrl($supplier);

                    $isVerified = (bool) $supplier->is_verified;
                    $isTrusted = (bool) $supplier->is_trusted;
                    $isPremium = (bool) $supplier->is_premium;

                    $supplierLevel = $supplier->level;
                @endphp

                <article
                    class="saved-supplier-card rounded-xl border border-gray-200
                           bg-white p-4 shadow-sm transition duration-200
                           hover:border-gray-300 hover:shadow-md sm:p-5"
                    data-supplier-id="{{ $supplier->id }}"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">

                        {{-- Logo --}}
                        <div class="flex h-20 w-20 flex-shrink-0 items-center
                                    justify-center overflow-hidden rounded-xl
                                    border border-gray-100 bg-gray-50">

                            @if ($logoUrl)
                                <img
                                    src="{{ $logoUrl }}"
                                    alt="{{ $supplierName }} logo"
                                    class="h-full w-full object-contain p-2"
                                    loading="lazy"
                                    decoding="async"
                                    onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.classList.remove('hidden');"
                                >

                                <div class="hidden h-full w-full items-center justify-center">
                                    <span class="text-2xl font-semibold text-gray-300">
                                        {{ \Illuminate\Support\Str::upper(
                                            \Illuminate\Support\Str::substr($supplierName, 0, 1)
                                        ) }}
                                    </span>
                                </div>
                            @else
                                <span class="text-2xl font-semibold text-gray-300"
                                      aria-hidden="true">
                                    {{ \Illuminate\Support\Str::upper(
                                        \Illuminate\Support\Str::substr($supplierName, 0, 1)
                                    ) }}
                                </span>
                            @endif

                        </div>

                        {{-- Supplier details --}}
                        <div class="min-w-0 flex-1">

                            <div class="flex flex-wrap items-center gap-2">

                                @if ($profileUrl)
                                    <a
                                        href="{{ $profileUrl }}"
                                        class="break-words text-base font-semibold text-gray-900
                                               transition hover:text-gray-600
                                               focus:outline-none focus:underline"
                                    >
                                        {{ $supplierName }}
                                    </a>
                                @else
                                    <h2 class="break-words text-base font-semibold text-gray-900">
                                        {{ $supplierName }}
                                    </h2>
                                @endif

                                @if ($isVerified)
                                    <span class="inline-flex items-center gap-1 rounded-md
                                                 bg-blue-50 px-2 py-1 text-xs font-medium
                                                 text-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-3.5 w-3.5"
                                             viewBox="0 0 20 20"
                                             fill="currentColor"
                                             aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M10 1.667 12.575 3l2.9.225 1.3 2.6 2.058 2.05-.7 2.825.7 2.825-2.058 2.05-1.3 2.6-2.9.225L10 18.333 7.425 17l-2.9-.225-1.3-2.6-2.058-2.05.7-2.825-.7-2.825 2.058-2.05 1.3-2.6 2.9-.225L10 1.667Zm3.04 5.81a.75.75 0 0 1 .066 1.06l-3.5 4a.75.75 0 0 1-1.09.03l-2-2a.75.75 0 1 1 1.06-1.06l1.43 1.43 2.974-3.394a.75.75 0 0 1 1.06-.066Z"
                                                  clip-rule="evenodd" />
                                        </svg>
                                        Verified
                                    </span>
                                @endif

                                @if ($isTrusted)
                                    <span class="rounded-md bg-emerald-50 px-2 py-1
                                                 text-xs font-medium text-emerald-700">
                                        Trusted
                                    </span>
                                @endif

                                @if ($isPremium)
                                    <span class="rounded-md bg-amber-50 px-2 py-1
                                                 text-xs font-medium text-amber-800">
                                        Premium
                                    </span>
                                @endif

                            </div>

                            @if ($description)
                                <p class="mt-2 text-sm leading-6 text-gray-600">
                                    {{ \Illuminate\Support\Str::limit($description, 220) }}
                                </p>
                            @else
                                <p class="mt-2 text-sm text-gray-400">
                                    Supplier profile
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2">

                                @if ($countryName)
                                    <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-4 w-4 flex-shrink-0 text-gray-400"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.7"
                                             aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                                            <circle cx="12" cy="10" r="2.5" />
                                        </svg>

                                        {{ $countryName }}
                                    </span>
                                @endif

                                @if ($supplierLevel)
                                    <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-4 w-4 flex-shrink-0 text-gray-400"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.7"
                                             aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="m12 3 2.7 5.47 6.03.88-4.36 4.25 1.03 6.01L12 16.77l-5.4 2.84 1.03-6.01-4.36-4.25 6.03-.88L12 3Z" />
                                        </svg>

                                        {{ $supplierLevel }} Supplier
                                    </span>
                                @endif

                            </div>

                            <p class="mt-3 text-xs text-gray-400">
                                Saved {{ $item->created_at?->format('M j, Y') }}
                            </p>

                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap gap-2 sm:flex-shrink-0 sm:justify-end">

                            @if ($profileUrl)
                                <a
                                    href="{{ $profileUrl }}"
                                    class="inline-flex min-h-10 items-center justify-center
                                           rounded-lg border border-gray-200 bg-white
                                           px-3 py-2 text-sm font-medium text-gray-700
                                           transition hover:bg-gray-50
                                           focus:outline-none focus:ring-2
                                           focus:ring-gray-300"
                                >
                                    View Profile
                                </a>
                            @endif

                            <button
                                type="button"
                                class="saved-supplier-remove inline-flex min-h-10
                                       items-center justify-center gap-2 rounded-lg
                                       px-3 py-2 text-sm font-medium text-gray-500
                                       transition hover:bg-red-50 hover:text-red-600
                                       focus:outline-none focus:ring-2
                                       focus:ring-red-200
                                       disabled:cursor-not-allowed disabled:opacity-50"
                                data-url="{{ route('buyer.saved-suppliers.destroy', $supplier) }}"
                                aria-label="Remove {{ $supplierName }} from saved suppliers"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4 7h16M10 11v6m4-6v6M5 7l1 14h12l1-14M9 7V4h6v3" />
                                </svg>

                                <span class="saved-supplier-remove-label">Remove</span>
                            </button>

                        </div>

                    </div>
                </article>

            @endforeach

        </div>

        {{-- Pagination --}}
        @if ($items->hasPages())
            <div class="border-t border-gray-200 pt-5">
                {{ $items->links() }}
            </div>
        @endif

    @else

        {{-- Empty state --}}
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-16
                    text-center sm:py-20">

            <div class="mx-auto flex h-16 w-16 items-center justify-center
                        rounded-full bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8 text-gray-400"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.5"
                     aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 10h8M8 14h5" />
                </svg>
            </div>

            <h2 class="mt-5 text-lg font-semibold text-gray-900">
                No saved suppliers yet
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                Save suppliers you are interested in to build your shortlist
                and return to their profiles later.
            </p>

            @if (\Illuminate\Support\Facades\Route::has('catalog.index'))
                <a
                    href="{{ route('catalog.index') }}"
                    class="mt-6 inline-flex min-h-11 items-center justify-center
                           rounded-lg bg-gray-900 px-5 py-3 text-sm font-semibold
                           text-white transition hover:bg-gray-800
                           focus:outline-none focus:ring-2 focus:ring-gray-400
                           focus:ring-offset-2"
                >
                    Explore Marketplace
                </a>
            @endif

        </div>

    @endif

    {{-- Future enhancement --}}
    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 sm:p-5">
        <div class="flex items-start gap-3">

            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center
                        rounded-lg bg-white text-gray-500 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.6"
                     aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 2" />
                </svg>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-800">
                    Supplier shortlist tools
                </h3>

                <p class="mt-1 text-sm leading-5 text-gray-500">
                    Supplier comparisons, notes, project-based lists and quick
                    access to supplier conversations can be introduced later.
                </p>

                <span class="mt-2 inline-flex rounded-md bg-white px-2 py-1
                             text-xs font-medium text-gray-500
                             ring-1 ring-inset ring-gray-200">
                    Future enhancement
                </span>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('saved-suppliers-list');
    const messageBox = document.getElementById('saved-suppliers-message');

    function showMessage(message, type = 'error') {
        if (!messageBox) return;

        messageBox.textContent = message;

        messageBox.classList.remove(
            'hidden',
            'border-red-200',
            'bg-red-50',
            'text-red-700',
            'border-green-200',
            'bg-green-50',
            'text-green-700'
        );

        if (type === 'success') {
            messageBox.classList.add(
                'border-green-200',
                'bg-green-50',
                'text-green-700'
            );
        } else {
            messageBox.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );
        }
    }

    document.querySelectorAll('.saved-supplier-remove').forEach(function (button) {
        button.addEventListener('click', async function () {
            if (button.disabled) return;

            const card = button.closest('.saved-supplier-card');

            if (!card) return;

            if (!window.confirm('Remove this supplier from your saved list?')) {
                return;
            }

            const label = button.querySelector('.saved-supplier-remove-label');
            const originalText = label ? label.textContent : 'Remove';

            button.disabled = true;

            if (label) {
                label.textContent = 'Removing…';
            }

            try {
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.content;

                if (!csrfToken) {
                    throw new Error(
                        'Security token is missing. Refresh the page and try again.'
                    );
                }

                const response = await fetch(button.dataset.url, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json().catch(function () {
                    return {};
                });

                if (!response.ok || data.status !== 'removed') {
                    if (response.status === 401 || response.status === 419) {
                        throw new Error(
                            'Your session may have expired. Refresh the page and try again.'
                        );
                    }

                    if (response.status === 403) {
                        throw new Error(
                            'You do not have permission to modify this list.'
                        );
                    }

                    throw new Error(
                        data.message || 'Unable to remove this supplier. Please try again.'
                    );
                }

                card.remove();

                const countElement = document.getElementById(
                    'saved-suppliers-count'
                );

                if (countElement && Number.isFinite(Number(data.count))) {
                    countElement.textContent = data.count;
                }

                if (typeof window.updateSavedSuppliersBadge === 'function') {
                    try {
                        await window.updateSavedSuppliersBadge(data.count);
                    } catch (error) {
                        console.error('Badge update failed:', error);
                    }
                }

                const remainingCards = list
                    ? list.querySelectorAll('.saved-supplier-card').length
                    : 0;

                if (remainingCards === 0) {
                    /*
                     * Reload so the server can render the empty state
                     * and handle the current pagination page correctly.
                     */
                    window.location.reload();
                    return;
                }

                showMessage(
                    data.message || 'Supplier removed from your saved list.',
                    'success'
                );

            } catch (error) {
                console.error('Saved supplier removal error:', error);

                showMessage(
                    error.message || 'Something went wrong. Please try again.'
                );

                button.disabled = false;

                if (label) {
                    label.textContent = originalText;
                }
            }
        });
    });
});
</script>
@endpush
