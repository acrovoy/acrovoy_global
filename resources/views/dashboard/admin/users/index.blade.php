@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <h1 class="text-2xl font-bold">Users</h1>

    <form method="GET" class="flex gap-2 items-center w-full md:w-auto">

        {{-- Поиск по имени или email --}}
        <input type="text"
               name="search"
               value="{{ $search ?? '' }}"
               placeholder="Search by name or email..."
               class="border border-gray-300 rounded-md px-3 py-2 text-sm w-full md:w-64 focus:ring-1 focus:ring-blue-500 focus:outline-none"
               onkeyup="if(event.key === 'Enter') this.form.submit()">

    </form>
</div>

<div class="overflow-x-auto border border-gray-200 rounded-lg">
    <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left font-medium text-gray-700">ID</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Name</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Email</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Status</th>
                <th class="px-4 py-2 text-left font-medium text-gray-700">Created</th>
                <th class="px-4 py-2 text-right font-medium text-gray-700">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @foreach($users as $user)
            <tr>
                <td class="px-4 py-2">{{ $user->id }}</td>
                <td class="px-4 py-2">{{ $user->name }}</td>
                <td class="px-4 py-2">{{ $user->email }}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $user->is_blocked ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                    </span>
                </td>
                <td class="px-4 py-2">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-4 py-2 text-right space-x-2">
                    {{-- Block / Unblock --}}
                    <form class="inline" action="{{ route('admin.users.toggleBlock', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="text-yellow-600 hover:text-yellow-800 font-medium px-2 py-1 rounded">
                            {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                        </button>
                    </form>

                    {{-- Edit --}}
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded">
                       Edit
                    </a>

                    {{-- Delete --}}
                    <form class="inline" action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


@endsection
