@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    <div>
        <h2 class="text-2xl font-semibold text-gray-900">
            Invite User
        </h2>

        <p class="text-sm text-gray-500">
            Send invitation to join your company
        </p>
    </div>


    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">

        <form method="POST" action="#">

            @csrf

            <div class="grid md:grid-cols-3 gap-4">

                <input
                    type="email"
                    name="email"
                    placeholder="User email"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    required>


                <select
                    name="role"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="admin">Admin</option>
                    <option value="member">Member</option>
                    <option value="viewer">Viewer</option>
                </select>


                <button
                    class="bg-gray-900 text-white rounded-lg text-sm px-4 py-2">
                    Send Invitation
                </button>

            </div>

        </form>

    </div>


    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b">

                <tr>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Email
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Role
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Status
                    </th>

                    <th class="px-5 py-3 text-right font-medium text-gray-600">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y">

                @foreach($invites ?? [] as $invite)

                <tr>

                    <td class="px-5 py-3 text-gray-700">
                        {{ $invite->email }}
                    </td>


                    <td class="px-5 py-3 text-gray-700">
                        {{ ucfirst($invite->role) }}
                    </td>


                    <td class="px-5 py-3">
                        @if($invite->accepted_at)
                        <span class="px-2 py-1 text-xs bg-green-50 text-green-700 rounded-full">
                            Accepted
                        </span>

                        @elseif($invite->expires_at && now()->gt($invite->expires_at))
                        <span class="px-2 py-1 text-xs bg-red-50 text-red-700 rounded-full">
                            Expired
                        </span>

                        @else
                        <span class="px-2 py-1 text-xs bg-yellow-50 text-yellow-700 rounded-full">
                            Pending
                        </span>
                        @endif
                    </td>


                    <td class="px-5 py-3 text-right">

                        <button class="text-sm text-gray-500 hover:text-gray-900">
                            Resend
                        </button>

                        <button class="text-sm text-red-500 ml-3">
                            Cancel
                        </button>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
@endsection