@extends('dashboard.admin.layout')

@section('dashboard-content')

@php
    $status = request('status');
    $userFilter = request('user');
@endphp

<div class="flex flex-col gap-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <p class="text-sm text-gray-500">Manage all platform products</p>
        </div>

        <form method="GET" class="flex gap-2 items-center">
            {{-- Сортировка --}}
            <select name="sort"
                    onchange="this.form.submit()"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white">
                <option value="">Newest</option>
                <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
            </select>

            {{-- Фильтр по статусу --}}
            <select name="status"
                    onchange="this.form.submit()"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white">
                <option value="">All</option>
                <option value="pending" @selected($status === 'pending')>Pending</option>
                <option value="approved" @selected($status === 'approved')>Approved</option>
                <option value="rejected" @selected($status === 'rejected')>Rejected</option>
            </select>

            {{-- Поиск по пользователю --}}
            <input type="text"
                   name="user"
                   value="{{ $userFilter ?? '' }}"
                   placeholder="Show by user..."
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700"
                   onkeydown="if(event.key === 'Enter') this.form.submit()">
        </form>

        <a href="#" class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
            + Create product
        </a>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">ID</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Title</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Created by</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Created</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr onclick="window.location='{{ route('admin.products.show', $product->id) }}'"
                        class="hover:bg-gray-50 transition cursor-pointer">

                        {{-- ID --}}
                        <td class="px-5 py-3 font-semibold text-gray-900">{{ $product->id }}</td>

                        {{-- Title --}}
                        <td class="px-5 py-3 font-medium text-gray-900">
                            {{ $product->name }}
                        </td>

                        {{-- Created by --}}
                        <td class="px-5 py-3 text-gray-700">
                            <div>
                            {{ $product->supplier->user->name ?? '—' }} {{ $product->supplier->user->last_name ?? '—' }}
                            </div>
                            <div class="text-emerald-600 text-xs">
                               {{ $product->supplier->name ?? '—' }} 
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-1 rounded text-xs font-medium
                                @if($product->status === 'pending') bg-yellow-50 text-yellow-700
                                @elseif($product->status === 'approved') bg-green-50 text-green-700
                                @else bg-red-50 text-red-700
                                @endif">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>

                        {{-- Created --}}
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            <div>{{ $product->created_at?->format('d M y') }}</div>
                            <div>{{ $product->created_at?->format('H:i') }}</div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Пагинация --}}
        <div class="mt-4 px-5">
            {{ $products->links('pagination::tailwind') }}
        </div>
    </div>

</div>

@endsection
