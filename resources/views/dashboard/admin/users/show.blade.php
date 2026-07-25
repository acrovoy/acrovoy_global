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

        <div class="flex items-center gap-3">

   <div class="flex flex-wrap items-center gap-3">

      {{-- Chat --}}
    <button
        class="open-conversation inline-flex items-center gap-2
               px-4 py-2
               text-sm font-medium text-gray-700
               bg-white border border-gray-200 rounded-lg
               hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
               transition-all duration-150 shadow-sm"
        data-subject-type="App\Models\User"
        data-subject-id="{{ $user->id }}">
        
        <span>Chat</span>
    </button>

   

    {{-- Block / Unblock --}}
<form id="block-user-form-{{ $user->id }}"
      action="{{ route('admin.users.toggleBlock', $user) }}"
      method="POST">

    @csrf
    @method('PATCH')

    <button type="button"
            onclick="confirmBlockUser({{ $user->id }}, {{ $user->is_blocked ? 'true' : 'false' }})"
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

        {{ $user->is_blocked ? '🔓 Unblock' : '🚫 Block' }}

    </button>

</form>

    {{-- Edit --}}
    <a href="{{ route('admin.users.edit',$user) }}"
       class="inline-flex items-center gap-2
              px-4 py-2
              text-sm font-medium text-gray-700
              bg-white border border-gray-200 rounded-lg
              hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
              transition-all duration-150 shadow-sm">

      
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
            class="inline-flex items-center gap-2
                   px-4 py-2
                   text-sm font-medium
                   text-red-600
                   bg-white
                   border border-red-200
                   rounded-lg
                   hover:bg-red-50
                   hover:border-red-300
                   transition-all duration-150
                   shadow-sm">

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
        <div class="bg-white border rounded-lg">

            <div class="border-b px-5 py-3 font-semibold">
                Personal Information
            </div>

            <div class="p-5 space-y-4">

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <div class="text-sm text-gray-500">First Name</div>
                        <div>{{ $user->name ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Last Name</div>
                        <div>{{ $user->last_name ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Email</div>
                        <div>{{ $user->email }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Platform Role</div>
                        <div>{{ $user->role ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Timezone</div>
                        <div>{{ $user->timezone ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Currency</div>
                        <div>{{ $user->currency ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Purchase Country</div>
                        <div>{{ $user->purchase_country ?: '-' }}</div>
                    </div>

                </div>

            </div>

        </div>


        {{-- Premium --}}
        <div class="bg-white border rounded-lg">

            <div class="border-b px-5 py-3 font-semibold">
                Premium Plans
            </div>

            <div class="p-5 space-y-4">

                <div>

                    <div class="text-sm text-gray-500">
                        Supplier Plan
                    </div>

                    <div class="font-semibold">
                        {{ optional($user->premiumPlan)->name ?? '-' }}
                    </div>

                    @if($user->supplier_premium_start)

                        <div class="text-sm text-gray-500">
                            {{ $user->supplier_premium_start }}
                            →
                            {{ $user->supplier_premium_end }}
                        </div>

                    @endif

                </div>

                <hr>

                <div>

                    <div class="text-sm text-gray-500">
                        Buyer Plan
                    </div>

                    <div class="font-semibold">
                        {{ optional($user->buyerPremiumPlan)->name ?? '-' }}
                    </div>

                    @if($user->buyer_premium_start)

                        <div class="text-sm text-gray-500">
                            {{ $user->buyer_premium_start }}
                            →
                            {{ $user->buyer_premium_end }}
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- Company Memberships --}}
<div class="bg-white border rounded-lg">

    <div class="border-b px-5 py-3 font-semibold">
        Company Memberships
    </div>


    <div class="overflow-x-auto">

        @if($user->companyMemberships->count())

        <table class="w-full text-sm">

            <thead class="bg-gray-50">

                <tr>
                    <th class="px-4 py-2 text-left">
                        Company
                    </th>

                    <th class="px-4 py-2 text-left">
                        Type
                    </th>

                    <th class="px-4 py-2 text-left">
                        Role
                    </th>

                    <th class="px-4 py-2 text-left">
                        Status
                    </th>

                    <th class="px-4 py-2 text-left">
                        Joined
                    </th>
                </tr>

            </thead>


            <tbody>

            @foreach($user->companyMemberships as $membership)

                <tr class="border-t">

                    <td class="px-4 py-3 font-medium">

                        {{ $membership->company?->name ?? '-' }}

                    </td>


                    <td class="px-4 py-3">

                        @php
                            $type = class_basename($membership->company_type);
                        @endphp

                        {{ $type }}

                    </td>


                    <td class="px-4 py-3">

                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100">
                            {{ ucfirst($membership->role) }}
                        </span>

                    </td>


                    <td class="px-4 py-3">

                        @if($membership->status === 'active')

                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                Active
                            </span>

                        @else

                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                {{ ucfirst($membership->status) }}
                            </span>

                        @endif

                    </td>


                    <td class="px-4 py-3">

                        {{ $membership->created_at?->format('Y-m-d') }}

                    </td>

                </tr>

            @endforeach


            </tbody>

        </table>

        @else

            <div class="p-5 text-gray-500">
                No company memberships found.
            </div>

        @endif

    </div>

</div>


   {{-- Addresses --}}
<div class="bg-white border rounded-lg">

    <div class="border-b px-5 py-3 font-semibold">
        User Addresses
    </div>


    <div class="p-5">

        @if($user->addresses->count())

            <div class="space-y-4">

            @foreach($user->addresses as $address)

                <div class="border rounded-lg p-4
                    {{ $address->is_default ? 'border-green-300 bg-green-50' : 'border-gray-200' }}">

                    <div class="flex justify-between items-start mb-3">

                        <div class="font-semibold">
                            {{ $address->first_name }}
                            {{ $address->last_name }}
                        </div>


                        @if($address->is_default)

                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                Default
                            </span>

                        @endif

                    </div>


                    <div class="grid md:grid-cols-2 gap-3 text-sm">


                                                <div>

                            <div class="text-gray-500">
                                Country
                            </div>

                            <div>
                                {{ $address->countryLocation?->name ?? '-' }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                Region
                            </div>

                            <div>
                                {{ $address->regionLocation?->name ?? '-' }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                City
                            </div>

                            <div>
                                {{ $address->city }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                Postal Code
                            </div>

                            <div>
                                {{ $address->postal_code }}
                            </div>

                        </div>



                        <div class="md:col-span-2">

                            <div class="text-gray-500">
                                Street
                            </div>

                            <div>
                                {{ $address->street }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                Phone
                            </div>

                            <div>
                                {{ $address->phone }}
                            </div>

                        </div>


                    </div>


                </div>

            @endforeach

            </div>


        @else

            <div class="text-gray-500">
                No addresses found.
            </div>

        @endif

    </div>

</div>


    {{-- Settings --}}
    <div class="bg-white border rounded-lg">

        <div class="border-b px-5 py-3 font-semibold">
            User Settings
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="text-left px-4 py-2">Key</th>
                        <th class="text-left px-4 py-2">Value</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($user->settings as $setting)

                    <tr class="border-t">

                        <td class="px-4 py-2 font-mono">
                            {{ $setting->key }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $setting->value }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-gray-500">
                            No settings found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<x-conversation.drawer
    subjectType="App\Models\User"
    :subjectId="$user->id"
    :messagesUrl="url('/dashboard/admin/messenger/conversations')"
/>



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