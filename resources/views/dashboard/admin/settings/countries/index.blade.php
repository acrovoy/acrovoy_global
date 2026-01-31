@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Countries</h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage available purchase countries
        </p>
    </div>

    <a href="{{ route('admin.settings.countries.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md
              hover:bg-gray-800 text-sm shadow-sm">
        <span class="text-lg leading-none">+</span>
        + Add Country
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-2 bg-green-100 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-x-auto rounded-lg shadow border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Active</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Priority</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Default</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sort Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($countries as $country)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $country->id }}</td>
                    <td class="px-6 py-4 font-mono uppercase">{{ $country->code }}</td>
                    <td class="px-6 py-4">{{ $country->name }}</td>

                    <!-- Active -->
                    <td class="px-6 py-4 text-center">
                        @if($country->is_active)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded">
                                Disabled
                            </span>
                        @endif
                    </td>

                    <!-- Priority -->
                    <td class="px-6 py-4 text-center">
                        @if($country->is_priority)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                Yes
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded">
                                No
                            </span>
                        @endif
                    </td>

                    <!-- Default -->
                    <td class="px-6 py-4 text-center">
                        @if($country->is_default)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">
                                Yes
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded">
                                No
                            </span>
                        @endif
                    </td>

                    <!-- Sort Order -->
                    <td class="px-6 py-4 text-center font-mono">{{ $country->sort_order ?? '-' }}</td>

                    <!-- Actions -->
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('admin.settings.countries.edit', $country) }}"
                           class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm shadow">
                            Edit
                        </a>

                        <form action="{{ route('admin.settings.countries.destroy', $country) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Delete country?')"
                                    class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm shadow">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        No countries found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
