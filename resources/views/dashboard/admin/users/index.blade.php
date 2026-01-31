@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Users</h1>

    <form method="GET" class="flex gap-2 items-center">

        {{-- Поиск по имени или email --}}
        <input type="text"
               name="search"
               value="{{ $search ?? '' }}"
               placeholder="Search by name or email..."
               class="border rounded-md px-2 py-1 text-sm"
               onkeyup="if(event.key === 'Enter') this.form.submit()">

    </form>
</div>

<table class="w-full border rounded text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Created</th>
            <th class="px-4 py-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $user->id }}</td>
            <td class="px-4 py-2">{{ $user->name }}</td>
            <td class="px-4 py-2">{{ $user->email }}</td>
            <td class="px-4 py-2">
                @if($user->is_blocked)
                    <span class="text-red-600 font-semibold">Blocked</span>
                @else
                    <span class="text-green-600 font-semibold">Active</span>
                @endif
            </td>
            <td class="px-4 py-2">{{ $user->created_at->format('Y-m-d H:i') }}</td>
            <td class="px-4 py-2 text-right space-x-2">
                {{-- Block / Unblock --}}
                <form class="inline" action="{{ route('admin.users.toggleBlock', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-2 py-1 text-sm rounded {{ $user->is_blocked ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                        {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                    </button>
                </form>

                {{-- Edit --}}
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">Edit</a>

                {{-- Delete --}}
                <form class="inline" action="{{ route('admin.users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-2 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
