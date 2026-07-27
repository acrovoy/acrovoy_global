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