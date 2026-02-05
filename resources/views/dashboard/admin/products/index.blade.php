@extends('dashboard.admin.layout')

@section('dashboard-content')

@php
    $status = request('status');
    $userFilter = request('user');
@endphp

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products</h1>

    <form method="GET" class="flex gap-2 items-center">

        {{-- Сортировка --}}
        <select name="sort"
                onchange="this.form.submit()"
                class="border rounded-md px-2 py-1 text-sm">
            <option value="">Newest</option>
            <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
        </select>

        {{-- Фильтр по статусу --}}
        <select name="status"
                onchange="this.form.submit()"
                class="border rounded-md px-2 py-1 text-sm">
            <option value="">All</option>
            <option value="pending" @selected($status === 'pending')>Pending</option>
            <option value="approved" @selected($status === 'approved')>Approved</option>
            <option value="rejected" @selected($status === 'rejected')>Rejected</option>
        </select>

        {{-- Поиск по пользователю --}}
        <input type="text"
               name="user"
               value="{{ $userFilter }}"
               placeholder="Show by user..."
               class="border rounded-md px-2 py-1 text-sm"
               onkeydown="if(event.key === 'Enter') this.form.submit()">
    </form>

    <a href="#" class="px-4 py-2 bg-brown-600 text-black rounded-md text-sm font-semibold">
        + Create product
    </a>
</div>

<table class="w-full border rounded text-sm overflow-hidden">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 text-left text-gray-700">ID</th>
            <th class="px-4 py-2 text-left text-gray-700">Title</th>
            <th class="px-4 py-2 text-left text-gray-700">Created by</th>
            <th class="px-4 py-2 text-left text-gray-700">Status</th>
            <th class="px-4 py-2 text-left text-gray-700">Created</th>
        </tr>
    </thead>

    <tbody>
    @forelse($products as $product)

        <tr
            onclick="window.location='{{ route('admin.products.show', $product->id) }}'"
            class="border-t cursor-pointer hover:bg-gray-50 transition"
        >
            {{-- ID --}}
            <td class="px-4 py-2">
                {{ $product->id }}
            </td>

            {{-- TITLE --}}
            <td class="px-4 py-2 font-medium">
                {{ $product->name }}
            </td>

            {{-- CREATED BY --}}
            <td class="px-4 py-2">
                {{ $product->supplier->user->name ?? '—' }}
            </td>

            {{-- STATUS --}}
            <td class="px-4 py-2">
                <span class="px-2 py-1 rounded text-xs font-semibold
                    @if($product->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($product->status === 'approved') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($product->status) }}
                </span>
            </td>

            {{-- CREATED --}}
            <td class="px-4 py-2">
                {{ $product->created_at?->format('Y-m-d') }}
            </td>
        </tr>

    @empty
        <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                No products found.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="mt-6">
    {{ $products->links() }}
</div>

@endsection
