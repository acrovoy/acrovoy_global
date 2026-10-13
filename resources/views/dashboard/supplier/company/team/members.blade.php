
@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h2 class="text-2xl font-semibold text-[#3B2F2F]">
                Team Members
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Manage your company team members and their access
            </p>
        </div>

        <a
            href="{{ route('supplier.team.invite') }}"
            class="inline-flex w-fit items-center justify-center gap-2
                   rounded-lg border border-[#3B2F2F]
                   bg-[#3B2F2F] px-4 py-2.5
                   text-sm font-medium text-white
                   shadow-sm transition
                   hover:bg-[#514040]
                   active:scale-[0.98]"
        >
            <span class="text-lg leading-none">+</span>
            <span>Invite Team Member</span>
        </a>

    </div>


    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif


    {{-- Team members --}}
    <div class="overflow-hidden rounded-xl border border-[#E8DFD2] bg-white shadow-sm">

        {{-- Desktop table --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-sm">

                <thead class="border-b border-[#E8DFD2] bg-[#F7F3EA]">
                    <tr>
                        <th class="px-5 py-4 text-left font-semibold text-[#3B2F2F]">
                            Team Member
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-[#3B2F2F]">
                            Role
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-[#3B2F2F]">
                            Status
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-[#3B2F2F]">
                            Joined
                        </th>

                        <th class="px-5 py-4 text-right font-semibold text-[#3B2F2F]">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#F0EAE1]">

                    @forelse($members ?? [] as $member)
                        <tr class="transition hover:bg-[#FCFAF6]">

                            {{-- User --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">

                                    @php
                                        $teamUser = $member->user;
                                        $fullName = trim(
                                            ($teamUser->name ?? '') . ' ' .
                                            ($teamUser->last_name ?? '')
                                        );

                                        $avatar = $teamUser
                                            ? $teamUser->avatar()
                                            : null;

                                        $avatarUrl = $avatar->cdn_url ?? null;
                                    @endphp

                                    @if($avatarUrl)
                                        <img
                                            src="{{ $avatarUrl }}"
                                            alt="{{ $fullName ?: 'Team member' }}"
                                            class="h-10 w-10 shrink-0 rounded-full border border-[#E8DFD2] object-cover"
                                        >
                                    @else
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EADCC5] text-sm font-semibold text-[#3B2F2F]">
                                            {{ strtoupper(substr($fullName ?: '?', 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-900">
                                            {{ $fullName ?: 'Unnamed user' }}
                                        </div>

                                        <div class="mt-0.5 break-all text-xs text-gray-500">
                                            {{ $teamUser->email ?? '—' }}
                                        </div>
                                    </div>

                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-md border border-[#E8DFD2] bg-[#F7F3EA] px-2.5 py-1 text-xs font-medium text-[#3B2F2F]">
                                    {{ ucfirst($member->role ?? 'member') }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4">
                                @if(($member->status ?? 'active') === 'active')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        {{ ucfirst($member->status ?? 'unknown') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Joined --}}
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-500">
                                {{ $member->created_at?->format('d M Y') ?? '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 text-right">
                                <span class="text-xs text-gray-400">
                                    —
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-14 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#F7F3EA] text-[#3B2F2F]">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="10" cy="7" r="4"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 8v6m3-3h-6"/>
                                    </svg>
                                </div>

                                <h3 class="mt-4 text-sm font-semibold text-[#3B2F2F]">
                                    No team members yet
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Invite a colleague to collaborate with your company.
                                </p>

                                <a
                                    href="{{ route('supplier.team.invite') }}"
                                    class="mt-4 inline-flex items-center rounded-lg bg-[#3B2F2F] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#514040]"
                                >
                                    Invite Team Member
                                </a>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>


        {{-- Mobile cards --}}
        <div class="divide-y divide-[#F0EAE1] md:hidden">

            @forelse($members ?? [] as $member)
                @php
                    $teamUser = $member->user;
                    $fullName = trim(
                        ($teamUser->name ?? '') . ' ' .
                        ($teamUser->last_name ?? '')
                    );

                    $avatar = $teamUser ? $teamUser->avatar() : null;
                    $avatarUrl = $avatar->cdn_url ?? null;
                @endphp

                <div class="p-4">

                    <div class="flex items-start gap-3">

                        @if($avatarUrl)
                            <img
                                src="{{ $avatarUrl }}"
                                alt="{{ $fullName ?: 'Team member' }}"
                                class="h-11 w-11 shrink-0 rounded-full border border-[#E8DFD2] object-cover"
                            >
                        @else
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#EADCC5] text-sm font-semibold text-[#3B2F2F]">
                                {{ strtoupper(substr($fullName ?: '?', 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="break-words font-medium text-gray-900">
                                {{ $fullName ?: 'Unnamed user' }}
                            </div>

                            <div class="mt-1 break-all text-xs text-gray-500">
                                {{ $teamUser->email ?? '—' }}
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="rounded-md border border-[#E8DFD2] bg-[#F7F3EA] px-2.5 py-1 text-xs font-medium text-[#3B2F2F]">
                                    {{ ucfirst($member->role ?? 'member') }}
                                </span>

                                @if(($member->status ?? 'active') === 'active')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                        {{ ucfirst($member->status ?? 'unknown') }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 text-xs text-gray-500">
                                Joined:
                                {{ $member->created_at?->format('d M Y') ?? '—' }}
                            </div>
                        </div>

                    </div>

                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <h3 class="text-sm font-semibold text-[#3B2F2F]">
                        No team members yet
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Invite a colleague to collaborate with your company.
                    </p>

                    <a
                        href="{{ route('supplier.team.invite') }}"
                        class="mt-4 inline-flex items-center rounded-lg bg-[#3B2F2F] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#514040]"
                    >
                        Invite Team Member
                    </a>
                </div>
            @endforelse

        </div>

    </div>

    <p class="text-xs leading-5 text-gray-400">
        Team access is managed through your company membership and role settings.
    </p>

</div>
@endsection
