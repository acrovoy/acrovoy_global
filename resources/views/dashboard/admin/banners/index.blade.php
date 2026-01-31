@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Banners & Notes</h1>

    
</div>

{{-- Фильтр по статусу --}}
<form method="GET" class="mb-4 flex gap-2 items-center">
    <select name="status" onchange="this.form.submit()" class="border rounded-md px-2 py-1 text-sm">
        <option value="">All</option>
        <option value="active" @selected($status === 'active')>Active</option>
        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
    </select>
</form>

<table class="w-full border rounded text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Title</th>
            <th class="px-4 py-2">Image</th>
            <th class="px-4 py-2">Created by</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Created</th>
            <th class="px-4 py-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($banners as $banner)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $banner['id'] }}</td>
            <td class="px-4 py-2">{{ $banner['title'] }}</td>
            <td class="px-4 py-2">
                <img src="{{ $banner['image'] }}" alt="{{ $banner['title'] }}" class="w-24 h-auto rounded">
            </td>
            <td class="px-4 py-2">{{ $banner['created_by'] ?? 'Admin' }}</td>
            <td class="px-4 py-2">{{ ucfirst($banner['status']) }}</td>
            <td class="px-4 py-2">{{ $banner['created_at'] }}</td>
            <td class="px-4 py-2 text-right space-x-2">
                <a href="#" class="text-blue-600 hover:underline">View</a>
                <a href="#" class="text-amber-600 hover:underline">Edit</a>
                <a href="#" class="text-red-600 hover:underline">Delete</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
