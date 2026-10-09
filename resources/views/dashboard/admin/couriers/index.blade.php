@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Couriers</h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage courier services available on Acrovoy.
            </p>
        </div>

        <a href="{{ route('admin.couriers.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800 transition">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Add courier
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET"
              action="{{ route('admin.couriers.index') }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-center">

            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by courier name or code..."
                       class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100">
            </div>

            <select name="status"
                    class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>

            <button type="submit"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Search
            </button>

            @if(request()->filled('search') || request()->filled('status'))
                <a href="{{ route('admin.couriers.index') }}"
                   class="text-center text-sm font-medium text-gray-500 hover:text-gray-900">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Couriers table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="font-semibold text-gray-900">Courier directory</h2>
            <span class="text-sm text-gray-500">
                {{ $couriers->total() }} {{ \Illuminate\Support\Str::plural('courier', $couriers->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Courier</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Code</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Website</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($couriers as $courier)
                        @php
                            $translation = $courier->translation();
                        @endphp

                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="flex min-w-[220px] items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                        @if($courier->logo)
    <img src="{{ asset('storage/' . ltrim($courier->logo, '/')) }}"
     alt="{{ $courier->translation(app()->getLocale())?->name ?? $courier->code }}"
     class="h-10 w-10 rounded-lg border border-stone-200 bg-white object-contain p-1">
@else
    <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-stone-200 bg-stone-50 text-xs text-stone-400">
        —
    </div>
@endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900">
                                            {{ $translation?->name ?: $courier->code }}
                                        </p>

                                        @if($translation?->description)
                                            <p class="mt-1 max-w-xs truncate text-xs text-gray-500">
                                                {{ $translation->description }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">No description</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex rounded-lg bg-gray-100 px-2.5 py-1 font-mono text-xs text-gray-700">
                                    {{ $courier->code }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                @if($courier->website)
                                    <a href="{{ $courier->website }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex max-w-[200px] items-center gap-1 truncate text-sm text-gray-600 hover:text-gray-900 hover:underline">
                                        {{ parse_url($courier->website, PHP_URL_HOST) ?: $courier->website }}
                                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 3h7v7M10 14 21 3"/>
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if($courier->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center text-sm tabular-nums text-gray-600">
                                {{ $courier->sort_order }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.couriers.edit', $courier) }}"
                                       title="Edit courier"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 transition">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="m16 4 4 4M4 20l4-.8L19 8a2.1 2.1 0 0 0-3-3L5 16l-1 4Z"/>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.couriers.toggle-active', $courier) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                title="{{ $courier->is_active ? 'Deactivate courier' : 'Activate courier' }}"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 transition">
                                            @if($courier->is_active)
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M12 3v9"/>
                                                    <path d="M7.1 5.8a8 8 0 1 0 9.8 0"/>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="m5 12 4 4L19 6"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('admin.couriers.destroy', $courier) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this courier? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                title="Delete courier"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-100 text-red-500 hover:border-red-200 hover:bg-red-50 transition">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 10v6m4-6v6"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100">
                                    <svg class="h-6 w-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M3 7h11v10H3z"/>
                                        <path d="M14 10h4l3 3v4h-7z"/>
                                        <circle cx="7.5" cy="18" r="1.5"/>
                                        <circle cx="17.5" cy="18" r="1.5"/>
                                    </svg>
                                </div>

                                <h3 class="mt-4 text-sm font-semibold text-gray-900">No couriers found</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Add a courier service to start building your directory.
                                </p>

                                <a href="{{ route('admin.couriers.create') }}"
                                   class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-gray-900 hover:underline">
                                    Add courier
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($couriers->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $couriers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

