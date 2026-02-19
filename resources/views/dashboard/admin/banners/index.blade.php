@extends('dashboard.admin.layout')

@section('dashboard-content')
@php
    $status = request('status');
@endphp

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Banners & Notes</h1>
        <p class="text-sm text-gray-500">Manage all banners and notes on the platform</p>
    </div>
</div>

{{-- ================= FILTER ================= --}}
<form method="GET" class="mb-4 flex gap-2 items-center">
    <select name="status"
            onchange="this.form.submit()"
            class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white">
        <option value="">All</option>
        <option value="active" @selected($status === 'active')>Active</option>
        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
    </select>
</form>

{{-- ================= TABLE ================= --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left font-medium text-gray-600">ID</th>
                <th class="px-5 py-3 text-left font-medium text-gray-600">Title</th>
                <th class="px-5 py-3 text-left font-medium text-gray-600">Image</th>
                <th class="px-5 py-3 text-left font-medium text-gray-600">Created by</th>
                <th class="px-5 py-3 text-left font-medium text-gray-600">Status</th>
                <th class="px-5 py-3 text-left font-medium text-gray-600">Created</th>
                <th class="px-5 py-3 text-right font-medium text-gray-600">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse($banners as $banner)
            <tr class="hover:bg-gray-50 transition cursor-pointer">
                <td class="px-5 py-3 font-semibold text-gray-900">{{ $banner['id'] }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $banner['title'] }}</td>
                <td class="px-5 py-3">
                    <img src="{{ $banner['image'] }}" alt="{{ $banner['title'] }}" class="w-24 h-auto rounded border">
                </td>
                <td class="px-5 py-3 text-gray-700">{{ $banner['created_by'] ?? 'Admin' }}</td>
                <td class="px-5 py-3">
                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium
                        @if($banner['status'] === 'active') bg-green-50 text-green-700
                        @else bg-gray-50 text-gray-700
                        @endif">
                        {{ ucfirst($banner['status']) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-600 text-xs">{{ $banner['created_at'] }}</td>
                <td class="px-5 py-3 text-right space-x-2">
                    <a href="#" class="text-blue-600 hover:underline">View</a>
                    <a href="#" class="text-amber-600 hover:underline">Edit</a>
                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-6 text-center text-gray-500">No banners found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    
</div>
@endsection
