@extends('dashboard.admin.layout')

@section('dashboard-content')

<a href="{{ route('admin.users.index') }}"
    class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to Users
</a>

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">

        <div class="flex items-center gap-5">

            @if($user->avatar())
            <img
                src="{{ $user->avatar()->cdn_url }}"
                class="w-20 h-20 rounded-full object-cover border"
                alt="{{ $user->full_name }}">
            @else
            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-600">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            @endif

            <div>
                <h1 class="text-3xl font-bold">
                    {{ $user->full_name ?: $user->name }}
                </h1>

                <div class="text-gray-500">
                    User #{{ $user->id }}
                </div>
            </div>

        </div>

       @php

    $settings = $user->settings->keyBy('key');

    $platformMode = $settings->get('platform_mode')?->value;

    $conversationSubjectType = \App\Models\User::class;
    $conversationSubjectId = $user->id;

    if ($platformMode === 'buyer') {

        $profile = \App\Models\Buyer::query()
            ->where('buyerable_type', \App\Models\User::class)
            ->where('buyerable_id', $user->id)
            ->first();

        if ($profile) {
            $conversationSubjectType = \App\Models\Buyer::class;
            $conversationSubjectId = $profile->id;
        }

    } elseif ($platformMode === 'supplier') {

        $profile = \App\Models\Supplier::query()
            ->where('supplierable_type', \App\Models\User::class)
            ->where('supplierable_id', $user->id)
            ->first();

        if ($profile) {
            $conversationSubjectType = \App\Models\Supplier::class;
            $conversationSubjectId = $profile->id;
        }

    }

@endphp


        <div class="flex items-center gap-3">

            <div class="flex flex-wrap items-center gap-3">

                {{-- Chat --}}
                <button
                    class="open-conversation inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg
               hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 transition-all duration-150 shadow-sm" data-subject-type="{{ $conversationSubjectType }}"
    data-subject-id="{{ $conversationSubjectId }}">
                    <span>Chat with User</span>
                </button>

                {{-- Block / Unblock --}}
                <form id="block-user-form-{{ $user->id }}"
                    action="{{ route('admin.users.toggleBlock', $user) }}"
                    method="POST">

                    @csrf
                    @method('PATCH')

                    <button type="button"
                        onclick="confirmBlockUser({{ $user->id }}, {{ $user->is_blocked ? 'true' : 'false' }})"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg
                   hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 active:scale-[0.98] transition-all duration-150 shadow-sm">

                        {{ $user->is_blocked ? '🔓 Unblock' : '🚫 Block' }}

                    </button>

                </form>

                {{-- Edit --}}
                <a href="{{ route('admin.users.edit',$user) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg
              hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 transition-all duration-150 shadow-sm">
                    <span>Edit</span>

                </a>

                {{-- Delete --}}
                <form id="delete-user-form-{{ $user->id }}"
                    action="{{ route('admin.users.destroy',$user) }}"
                    method="POST">

                    @csrf
                    @method('DELETE')

                    <button type="button"
                        onclick="confirmDeleteUser({{ $user->id }})"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg
                   hover:bg-red-50 hover:border-red-300 transition-all duration-150 shadow-sm">
                        <span>Delete</span>

                    </button>

                </form>

            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="grid lg:grid-cols-4 gap-4">

        <div class="bg-white border rounded-lg p-5">

            <div class="text-gray-500 text-sm">
                Status
            </div>

            <div class="mt-2">

                @if($user->is_blocked)
                <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-semibold">
                    Blocked
                </span>
                @else
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                    Active
                </span>
                @endif

            </div>

        </div>

        <div class="bg-white border rounded-lg p-5">

            <div class="text-gray-500 text-sm">
                Email Verified
            </div>

            <div class="mt-2 font-semibold">

                @if($user->email_verified_at)
                Yes
                @else
                No
                @endif

            </div>

        </div>

        <div class="bg-white border rounded-lg p-5">

            <div class="text-gray-500 text-sm">
                Registered
            </div>

            <div class="mt-2 font-semibold">
                {{ $user->created_at?->format('Y-m-d H:i') }}
            </div>

        </div>

        <div class="bg-white border rounded-lg p-5">

            <div class="text-gray-500 text-sm">
                Last Update
            </div>

            <div class="mt-2 font-semibold">
                {{ $user->updated_at?->format('Y-m-d H:i') }}
            </div>

        </div>

    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Personal --}}

        @include('dashboard.admin.users.partials.personal-information')

        {{-- Premium --}}

        @include('dashboard.admin.users.partials.premium-plans')

    </div>

    {{-- Company Memberships --}}

    @include('dashboard.admin.users.partials.company-membership')

    {{-- Settings --}}

    @include('dashboard.admin.users.partials.user-settings-saved')

    {{-- Addresses --}}

    @include('dashboard.admin.users.partials.user-addresses')

</div>

<x-conversation.drawer
    :subjectType="$conversationSubjectType"
    :subjectId="$conversationSubjectId"
    :messagesUrl="url('/dashboard/admin/messenger/conversations')" />

<script>

function confirmBlockUser(userId, isBlocked)
{
    window.confirmModal.open({

        title: isBlocked
            ? 'Unblock User'
            : 'Block User',

        message: isBlocked
            ? 'Do you want to unblock this user?'
            : 'Do you want to block this user?',

        description: isBlocked
            ? 'The user will be able to access the platform again.'
            : 'The user will lose access to the platform.',

        confirmText: isBlocked
            ? 'Unblock'
            : 'Block',

        cancelText: 'Cancel',

        type: isBlocked
            ? 'success'
            : 'warning',

        onConfirm: function () {

            document
                .getElementById(
                    'block-user-form-' + userId
                )
                .submit();

        }

    });
}

function confirmDeleteUser(userId)
{
    window.confirmModal.open({

        title: 'Permanently Delete User?',

        message: 'Are you sure you want to delete this user?',

        description:
            'This action cannot be undone.',

        confirmText: 'Delete',

        cancelText: 'Cancel',

        type: 'danger',

        onConfirm: function () {

            document
                .getElementById(
                    'delete-user-form-' + userId
                )
                .submit();

        }

    });
}

</script>


@endsection