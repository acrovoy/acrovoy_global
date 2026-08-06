@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex flex-col gap-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Collections</h1>
            <p class="text-sm text-gray-500">
                Manage platform and supplier collections
            </p>
        </div>

        <a href="{{ route('admin.collections.create') }}"
           class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
            + Create Collection
        </a>

    </div>

   <x-alerts />

    {{-- ================= TABLE ================= --}}

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b">

            <tr>

                <th class="px-5 py-3 text-left font-medium text-gray-600">
                    ID
                </th>

                <th class="px-5 py-3 text-left font-medium text-gray-600">
                    Collection
                </th>

                <th class="px-5 py-3 text-left font-medium text-gray-600">
                    Owner
                </th>

                <th class="px-5 py-3 text-left font-medium text-gray-600">
                    Products
                </th>

                <th class="px-5 py-3 text-left font-medium text-gray-600">
                    Visibility
                </th>

                <th class="px-5 py-3 text-left font-medium text-gray-600">
                    Created
                </th>

            </tr>

            </thead>

            <tbody class="divide-y">

            @forelse($collections as $collection)

                <tr
                    onclick="window.location='{{ route('admin.collections.edit',$collection) }}'"
                    class="hover:bg-gray-50 transition cursor-pointer">

                    {{-- ID --}}
                    <td class="px-5 py-3 font-semibold text-gray-900">

                        {{ $collection->public_id }}

                    </td>

                    {{-- Cover + Title --}}
                    <td class="px-5 py-3">

                        <div class="flex items-center gap-3">

                            <img
                                src="{{ $collection->cover?->cdn_url ?? asset('/images/no-image.png') }}"
                                class="w-12 h-12 rounded-lg border object-cover">

                            <div>

                                <div class="font-semibold text-gray-900">

                                    {{ $collection->currentTranslation?->title
                                        ?? 'Untitled Collection' }}

                                </div>

                                <div class="text-xs text-gray-500">

                                    {{ $collection->slug }}

                                </div>

                            </div>

                        </div>

                    </td>

                    {{-- Owner --}}
                    <td class="px-5 py-3">

                        <div class="font-medium text-gray-800">

                            {{ class_basename($collection->owner_type) }}

                        </div>

                        <div class="text-xs text-gray-500">

                            #{{ $collection->owner_id }}

                        </div>

                    </td>

                    {{-- Products --}}
                    <td class="px-5 py-3">

                        <span class="font-semibold">

                            {{ $collection->products_count ?? $collection->products_count }}

                        </span>

                    </td>

                    {{-- Visibility --}}
                    <td class="px-5 py-3">

                        <span
                            class="inline-flex px-2 py-1 rounded text-xs font-medium

                            @if($collection->visibility === 'public')
                                bg-green-50 text-green-700

                            @elseif($collection->visibility === 'draft')
                                bg-yellow-50 text-yellow-700

                            @else
                                bg-gray-100 text-gray-700
                            @endif">

                            {{ ucfirst($collection->visibility) }}

                        </span>

                    </td>

                    {{-- Created --}}
                    <td class="px-5 py-3 text-xs text-gray-500">

                        <div>

                            {{ $collection->created_at?->format('d M Y') }}

                        </div>

                        <div>

                            {{ $collection->created_at?->format('H:i') }}

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6"
                        class="px-6 py-10 text-center text-gray-500">

                        No collections found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="px-5 py-4 border-t">

            {{ $collections->links('pagination::tailwind') }}

        </div>

    </div>

</div>

@endsection