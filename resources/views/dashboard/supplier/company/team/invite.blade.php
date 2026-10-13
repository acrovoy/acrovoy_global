@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Page header --}}
    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-semibold text-[#3B2F2F]">
            Invite Team Members
        </h2>

        <p class="text-sm text-gray-500">
            Invite people to collaborate with your company on Acrovoy.
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

    @if (session('status'))
        <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
            <p class="mb-1 text-sm font-medium text-red-800">
                Please check the following:
            </p>

            <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Invite form --}}
    <div class="overflow-hidden rounded-xl border border-[#E7DED2] bg-white shadow-sm">

        <div class="border-b border-[#E7DED2] bg-[#FBF9F5] px-5 py-4 sm:px-6">
            <h3 class="text-base font-semibold text-[#3B2F2F]">
                Send an Invitation
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                The invited person will receive an email with instructions to join your team.
            </p>
        </div>

        <form method="POST" action="{{ route('supplier.team.invite.send') }}" class="p-5 sm:p-6">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">

                <div class="md:col-span-6">
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Email address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="name@company.com"
                        autocomplete="email"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-[#8B735B] focus:outline-none focus:ring-2 focus:ring-[#8B735B]/20 @error('email') border-red-400 @enderror"
                    >
                </div>

                <div class="md:col-span-3">
                    <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Team role
                    </label>

                    <select
    name="role"
    id="role"
    required
    class="w-full rounded-lg border border-[#E8DFD2] bg-white px-3 py-2.5 text-sm text-[#3B2F2F] focus:border-[#3B2F2F] focus:outline-none focus:ring-1 focus:ring-[#3B2F2F]"
>
    <option value="sales" @selected(old('role', 'sales') === 'sales')>
        Sales
    </option>

    <option value="administrator" @selected(old('role') === 'administrator')>
        Administrator
    </option>

    <option value="representative" @selected(old('role') === 'representative')>
        Representative
    </option>
</select>
                </div>

                <div class="md:col-span-3">
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#3B2F2F] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#51413F] focus:outline-none focus:ring-2 focus:ring-[#8B735B] focus:ring-offset-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
                        </svg>

                        Send Invitation
                    </button>
                </div>

            </div>

            <p class="mt-3 text-xs leading-5 text-gray-500">
                Invitations are valid for 7 days. The invited person must use the invited email address.
            </p>
        </form>
    </div>

    {{-- Invitations table --}}
    <div class="overflow-hidden rounded-xl border border-[#E7DED2] bg-white shadow-sm">

        <div class="flex flex-col gap-1 border-b border-[#E7DED2] bg-[#FBF9F5] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-base font-semibold text-[#3B2F2F]">
                    Team Invitations
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Manage invitations sent to prospective team members.
                </p>
            </div>

            <span class="text-sm text-gray-500">
                {{ isset($invites) ? $invites->count() : 0 }} invitations
            </span>
        </div>

        @if(isset($invites) && $invites->isNotEmpty())

            {{-- Desktop table --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">

                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 font-medium text-gray-600">
                                Email
                            </th>

                            <th class="px-6 py-3 font-medium text-gray-600">
                                Role
                            </th>

                            <th class="px-6 py-3 font-medium text-gray-600">
                                Sent
                            </th>

                            <th class="px-6 py-3 font-medium text-gray-600">
                                Expires
                            </th>

                            <th class="px-6 py-3 font-medium text-gray-600">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($invites as $invite)

                            @php
                                if ($invite->accepted_at) {
                                    $inviteStatus = 'Accepted';
                                    $statusClasses = 'bg-green-50 text-green-700 ring-green-600/20';
                                } elseif (!$invite->expires_at || now()->greaterThanOrEqualTo($invite->expires_at)) {
                                    $inviteStatus = 'Expired';
                                    $statusClasses = 'bg-red-50 text-red-700 ring-red-600/20';
                                } else {
                                    $inviteStatus = 'Pending';
                                    $statusClasses = 'bg-amber-50 text-amber-700 ring-amber-600/20';
                                }
                            @endphp

                            <tr class="transition hover:bg-[#FBF9F5]/70">

                                <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-800">
                                    {{ $invite->email }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                    {{ ucfirst($invite->role) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-gray-500">
                                    {{ $invite->created_at?->format('d M Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-gray-500">
                                    {{ $invite->expires_at?->format('d M Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
                                        {{ $inviteStatus }}
                                    </span>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="divide-y divide-gray-100 md:hidden">
                @foreach($invites as $invite)

                    @php
                        if ($invite->accepted_at) {
                            $inviteStatus = 'Accepted';
                            $statusClasses = 'bg-green-50 text-green-700';
                        } elseif (!$invite->expires_at || now()->greaterThanOrEqualTo($invite->expires_at)) {
                            $inviteStatus = 'Expired';
                            $statusClasses = 'bg-red-50 text-red-700';
                        } else {
                            $inviteStatus = 'Pending';
                            $statusClasses = 'bg-amber-50 text-amber-700';
                        }
                    @endphp

                    <div class="space-y-3 p-4">

                        <div class="flex items-start justify-between gap-3">
                            <p class="break-all text-sm font-medium text-gray-800">
                                {{ $invite->email }}
                            </p>

                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">
                                {{ $inviteStatus }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">Role</p>
                                <p class="mt-1 text-gray-700">{{ ucfirst($invite->role) }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Expires</p>
                                <p class="mt-1 text-gray-700">
                                    {{ $invite->expires_at?->format('d M Y') ?? '—' }}
                                </p>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        @else

            <div class="px-5 py-12 text-center sm:px-6">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#F7F3EA] text-[#6B5744]">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0-9.75 6.75L2.25 6.75"/>
                    </svg>
                </div>

                <h4 class="mt-4 text-sm font-semibold text-gray-800">
                    No invitations yet
                </h4>

                <p class="mx-auto mt-1 max-w-sm text-sm text-gray-500">
                    Send your first invitation using the form above to start building your team.
                </p>
            </div>

        @endif

    </div>

</div>
@endsection
