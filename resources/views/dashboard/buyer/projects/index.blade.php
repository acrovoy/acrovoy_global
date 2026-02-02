@extends('dashboard.layout')

@section('dashboard-content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-semibold">My Projects</h2>
        <p class="text-sm text-gray-500">
            Here you can view, edit, and manage all your projects and their items.
        </p>
    </div>

    <a href="{{ route('buyer.projects.create') }}"
       class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
        + Create New Project
    </a>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded border border-green-200 bg-green-50 text-green-800 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white border rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr class="text-left text-gray-600">
                <th class="px-5 py-3 font-medium">ID</th>
                <th class="px-5 py-3 font-medium">Title</th>
                <th class="px-5 py-3 font-medium">Category</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Items</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse($projects as $project)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-mono text-gray-800">{{ $project->id }}</td>
                    <td class="px-5 py-3 text-gray-800">{{ $project->title }}</td>
                    <td class="px-5 py-3 text-gray-800">{{ $project->category->name ?? '-' }}</td>

                    {{-- Status badge --}}
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                            @if($project->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($project->status == 'approved') bg-green-100 text-green-800
                            @elseif($project->status == 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>

                    <td class="px-5 py-3 text-gray-800">{{ $project->items_count ?? 0 }}</td>

                    {{-- Actions --}}
                    <td class="px-5 py-3 text-right space-x-2">
                        <a href="{{ route('buyer.projects.show', $project) }}" class="text-blue-600 hover:underline text-sm">View</a>
                        <a href="{{ route('buyer.projects.edit', $project) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>
                        <form action="{{ route('buyer.projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-4 text-center text-gray-500">
                        No projects found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
