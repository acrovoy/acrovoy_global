@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    <div class="flex justify-between items-center">

        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Team Members
            </h2>
            <p class="text-sm text-gray-500">
                Manage your company users and roles
            </p>
        </div>

        <a href="{{ route('supplier.team.invite') }}"
           class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
            Invite User
        </a>

    </div>


    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        User
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Role
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Status
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Joined
                    </th>

                    <th class="px-5 py-3 text-right font-medium text-gray-600">
                        Actions
                    </th>
                </tr>
            </thead>


            <tbody class="divide-y">

                @foreach($members ?? [] as $member)

                <tr class="hover:bg-gray-50 transition">

                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-900">
                            {{ $member->user->name ?? '-' }}
                        </div>

                        <div class="text-xs text-gray-400">
                            {{ $member->user->email ?? '-' }}
                        </div>
                    </td>


                    <td class="px-5 py-3 text-gray-700">
                        {{ ucfirst($member->role) }}
                    </td>


                    <td class="px-5 py-3">
                        <span class="px-2 py-1 text-xs bg-green-50 text-green-700 rounded-full">
                            Active
                        </span>
                    </td>


                    <td class="px-5 py-3 text-gray-600 text-xs">
                        {{ optional($member->created_at)->format('d M Y') }}
                    </td>


                    <td class="px-5 py-3 text-right">

                        <button class="text-sm text-gray-500 hover:text-gray-900">
                            Change Role
                        </button>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
@endsection