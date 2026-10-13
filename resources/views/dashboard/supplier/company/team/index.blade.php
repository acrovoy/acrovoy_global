
@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Page header --}}
    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-semibold text-[#3B2F2F]">
            Team Overview
        </h2>

        <p class="text-sm text-gray-500">
            Manage your company team members, invitations and permissions.
        </p>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Statistics --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

        {{-- Members --}}
        <div class="rounded-xl border border-[#E7DED2] bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-500">
                        Team Members
                    </p>

                    <p class="mt-2 text-3xl font-semibold tracking-tight text-[#3B2F2F]">
                        {{ $membersCount ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Registered company members
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#F7F3EA] text-[#6B5744]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="10" cy="7" r="4"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending invitations --}}
        <div class="rounded-xl border border-[#E7DED2] bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-500">
                        Pending Invitations
                    </p>

                    <p class="mt-2 text-3xl font-semibold tracking-tight text-[#3B2F2F]">
                        {{ $pendingInvitesCount ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Waiting for acceptance
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#F7F3EA] text-[#6B5744]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m3 7 9 6 9-6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Roles --}}
        <div class="rounded-xl border border-[#E7DED2] bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-500">
                        Available Roles
                    </p>

                    <p class="mt-2 text-3xl font-semibold tracking-tight text-[#3B2F2F]">
                        {{ $rolesCount ?? 3 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Access levels for your team
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#F7F3EA] text-[#6B5744]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3 4.5 6v5.5c0 4.5 3.2 7.7 7.5 9.5 4.3-1.8 7.5-5 7.5-9.5V6L12 3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Team management actions --}}
    <div class="overflow-hidden rounded-xl border border-[#E7DED2] bg-white shadow-sm">

        <div class="border-b border-[#E7DED2] bg-[#FBF9F5] px-5 py-4 sm:px-6">
            <h3 class="text-base font-semibold text-[#3B2F2F]">
                Team Management
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Choose what you would like to manage.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-3">

            {{-- Members --}}
            <a href="{{ route('supplier.team.members') }}"
               class="group flex items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-[#B9A58D] hover:bg-[#FBF9F5]">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#F7F3EA] text-[#6B5744] transition group-hover:bg-[#EADCC5]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="10" cy="7" r="4"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-[#3B2F2F]">
                        Manage Members
                    </p>

                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        View team members and their current access.
                    </p>

                    <span class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-[#6B5744]">
                        View members
                        <span aria-hidden="true">→</span>
                    </span>
                </div>
            </a>

            {{-- Invitations --}}
            <a href="{{ route('supplier.team.invite') }}"
               class="group flex items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-[#B9A58D] hover:bg-[#FBF9F5]">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#F7F3EA] text-[#6B5744] transition group-hover:bg-[#EADCC5]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5v14m-7-7h14"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-[#3B2F2F]">
                        Invite Team Members
                    </p>

                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        Invite colleagues to join your company.
                    </p>

                    <span class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-[#6B5744]">
                        Manage invitations
                        <span aria-hidden="true">→</span>
                    </span>
                </div>
            </a>

            {{-- Roles --}}
            <a href="{{ route('supplier.team.roles') }}"
               class="group flex items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-[#B9A58D] hover:bg-[#FBF9F5]">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#F7F3EA] text-[#6B5744] transition group-hover:bg-[#EADCC5]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3 4.5 6v5.5c0 4.5 3.2 7.7 7.5 9.5 4.3-1.8 7.5-5 7.5-9.5V6L12 3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m9 12 2 2 4-4"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-[#3B2F2F]">
                        Roles & Permissions
                    </p>

                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        Review team roles and their access levels.
                    </p>

                    <span class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-[#6B5744]">
                        View permissions
                        <span aria-hidden="true">→</span>
                    </span>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection
