@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex flex-col gap-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pages</h1>
            <p class="text-sm text-gray-500">
                Manage static pages and website content
            </p>
        </div>

        <a href="{{ route('admin.pages.create') }}"
           class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
            + Create Page
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
                        Page
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Template
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Status
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Published
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Updated
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y">

            @forelse($pages as $page)

                <tr
    onclick="window.location='{{ route('admin.pages.edit',$page) }}'"
    class="hover:bg-gray-50 transition cursor-pointer">

                    {{-- ID --}}
                    <td class="px-5 py-3 font-semibold text-gray-900">
                        {{ $page->public_id }}
                    </td>

                    {{-- Page --}}
                    <td class="px-5 py-3">

                        <div class="font-semibold text-gray-900">
                            {{ $page->title ?: 'Untitled Page' }}
                        </div>

                        <div class="text-xs text-gray-500">
                            /{{ $page->slug }}
                        </div>

                    </td>

                    {{-- Template --}}
                    <td class="px-5 py-3">

                        <span class="text-gray-700">
                            {{ ucfirst($page->template) }}
                        </span>

                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-3">

                        <span
                            class="inline-flex px-2 py-1 rounded text-xs font-medium

                            @if($page->status === 'published')
                                bg-green-50 text-green-700

                            @elseif($page->status === 'draft')
                                bg-yellow-50 text-yellow-700

                            @else
                                bg-gray-100 text-gray-700
                            @endif">

                            {{ ucfirst($page->status) }}

                        </span>

                    </td>

                    {{-- Published --}}
                    <td class="px-5 py-3 text-xs text-gray-500">

                        @if($page->published_at)

                            <div>
                                {{ $page->published_at->format('d M Y') }}
                            </div>

                            <div>
                                {{ $page->published_at->format('H:i') }}
                            </div>

                        @else

                            —

                        @endif

                    </td>

                    {{-- Updated --}}
                    <td class="px-5 py-3 text-xs text-gray-500">

                        <div>
                            {{ $page->updated_at?->format('d M Y') }}
                        </div>

                        <div>
                            {{ $page->updated_at?->format('H:i') }}
                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6"
                        class="px-6 py-10 text-center text-gray-500">

                        No pages found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="px-5 py-4 border-t">

            {{ $pages->links('pagination::tailwind') }}

        </div>

    </div>

</div>

@endsection