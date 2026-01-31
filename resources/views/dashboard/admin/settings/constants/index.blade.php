{{-- resources/views/dashboard/admin/settings/constants/index.blade.php --}}

@extends('dashboard.admin.settings.layout')

@section('settings-content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Constant</h1>
        <a href=""
           class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            + Add Constant
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($constants as $constant)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $constant->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $constant->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $constant->value }}</td>
                        <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                            <a href=""
                               class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded shadow transition">
                                Edit
                            </a>
                            <form action="" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded shadow transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Нет констант</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
