@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    <div>
        <h2 class="text-2xl font-semibold text-gray-900">
            Team Overview
        </h2>
        <p class="text-sm text-gray-500">
            Manage your company team members and permissions
        </p>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <div class="text-sm text-gray-500">Members</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">
                {{ $membersCount ?? 0 }}
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <div class="text-sm text-gray-500">Pending Invitations</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">
                {{ $pendingInvitesCount ?? 0 }}
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <div class="text-sm text-gray-500">Roles Available</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">
                {{ $rolesCount ?? 4 }}
            </div>
        </div>

    </div>


    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex gap-3">

        <a href="{{ route('supplier.team.members') }}"
           class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
            Manage Members
        </a>

        <a href="{{ route('supplier.team.invite') }}"
           class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            Invite User
        </a>

        <a href="{{ route('supplier.team.roles') }}"
           class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            Roles & Permissions
        </a>

    </div>

</div>
@endsection