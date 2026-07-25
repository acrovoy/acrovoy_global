@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Users
            </h2>
            <p class="text-sm text-gray-500">
                Browse, manage, and monitor all registered platform users
            </p>
        </div>

    

    <form method="GET" class="flex gap-2 items-center w-full md:w-auto">

    <input type="text"
           name="search"
           value="{{ $search ?? '' }}"
           placeholder="Search by name or email..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full md:w-72">

    <button type="submit"
            class="inline-flex items-center gap-2
                   px-4 py-2
                   text-sm font-medium
                   text-gray-700
                   bg-white
                   border border-gray-200
                   rounded-lg
                   hover:bg-gray-50
                   hover:border-gray-300
                   hover:text-gray-900
                   active:scale-[0.98]
                   transition-all duration-150
                   shadow-sm">

        Search

    </button>

</form>
</div>

@php
    function sortDirection($column, $sort, $direction)
    {
        return $sort === $column && $direction === 'asc'
            ? 'desc'
            : 'asc';
    }
@endphp

<div class="overflow-x-auto border border-gray-200 rounded-lg">
    <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                 {{-- ID --}}
    <th class="px-4 py-2 text-left font-medium text-gray-700">
        <a href="{{ route('admin.users.index', array_merge(request()->query(), [
            'sort' => 'id',
            'direction' => $sort === 'id' && $direction === 'asc' ? 'desc' : 'asc'
        ])) }}"
           class="inline-flex items-center gap-1 hover:text-black">

            ID

            @if($sort === 'id')
                {{ $direction === 'asc' ? '↑' : '↓' }}
            @endif

        </a>
    </th>

    {{-- Name --}}
    <th class="px-4 py-2 text-left font-medium text-gray-700">
        <a href="{{ route('admin.users.index', array_merge(request()->query(), [
            'sort' => 'name',
            'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'
        ])) }}"
           class="inline-flex items-center gap-1 hover:text-black">

            Name

            @if($sort === 'name')
                {{ $direction === 'asc' ? '↑' : '↓' }}
            @endif

        </a>
    </th>

    {{-- Email --}}
    <th class="px-4 py-2 text-left font-medium text-gray-700">
        <a href="{{ route('admin.users.index', array_merge(request()->query(), [
            'sort' => 'email',
            'direction' => $sort === 'email' && $direction === 'asc' ? 'desc' : 'asc'
        ])) }}"
           class="inline-flex items-center gap-1 hover:text-black">

            Email

            @if($sort === 'email')
                {{ $direction === 'asc' ? '↑' : '↓' }}
            @endif

        </a>
    </th>

    {{-- Status --}}
    <th class="px-4 py-2 text-left font-medium text-gray-700">
        <a href="{{ route('admin.users.index', array_merge(request()->query(), [
            'sort' => 'is_blocked',
            'direction' => $sort === 'is_blocked' && $direction === 'asc' ? 'desc' : 'asc'
        ])) }}"
           class="inline-flex items-center gap-1 hover:text-black">

            Status

            @if($sort === 'is_blocked')
                {{ $direction === 'asc' ? '↑' : '↓' }}
            @endif

        </a>
    </th>

    {{-- Created --}}
    <th class="px-4 py-2 text-left font-medium text-gray-700">
        <a href="{{ route('admin.users.index', array_merge(request()->query(), [
            'sort' => 'created_at',
            'direction' => $sort === 'created_at' && $direction === 'asc' ? 'desc' : 'asc'
        ])) }}"
           class="inline-flex items-center gap-1 hover:text-black">

            Registered

            @if($sort === 'created_at')
                {{ $direction === 'asc' ? '↑' : '↓' }}
            @endif

        </a>
    </th>
                
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @foreach($users as $user)
            <tr
            onclick="window.location='{{ route('admin.users.show', $user) }}'"
    class="cursor-pointer hover:bg-gray-50 transition-colors"
    >
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
                
            </tr>
        @endforeach
        </tbody>
    </table>
</div>




@endsection
