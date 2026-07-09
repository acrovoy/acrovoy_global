@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Companies
            </h2>

            <p class="text-sm text-gray-500">
                Manage all companies in the system
            </p>
        </div>

        <a href="{{ route('dashboard.companies.create') }}"
   class="inline-flex items-center gap-2 px-4 py-2
          text-sm font-medium text-gray-700
          bg-white border border-gray-200
          rounded-lg
          hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
          active:scale-[0.98]
          transition-all duration-150 shadow-sm">

    <span class="text-lg leading-none">+</span>
    <span>Add New Company</span>

</a>
    </div>

    <x-alerts />

    @php
    $typeLabel = function ($type) {
    return match($type) {
    'buyer' => 'Buyer Company',
    'supplier' => 'Supplier Company',
    'logistics' => 'Logistics Company',
    default => ucfirst($type),
    };
    };
    @endphp

    {{-- ACTIVE --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-3">
            Active Companies
        </h3>

        <div class="bg-white border rounded-xl overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">

                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach($activeCompanies as $company)

                    @php
                    $statusClass = match($company->status) {
                    'active' => 'bg-green-100 text-green-700',
                    'pending' => 'bg-gray-200 text-gray-700',
                    default => 'bg-gray-100 text-gray-700',
                    };
                    @endphp

                    <tr>
                        <td class="px-5 py-3 font-medium text-gray-900">
                            {{ $company->name }}
                        </td>

                        <td class="px-5 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                {{ $typeLabel($company->type) }}
                            </span>
                        </td>

                        <td class="px-5 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                {{ $company->status }}
                            </span>
                        </td>

                        <td class="px-5 py-3 text-right space-x-2">

                            {{-- TRANSFER OWNER --}}
                            <button
                                onclick="openOwnerDrawer({{ $company->id }})"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium
                                           text-gray-600 bg-gray-100 border border-gray-200
                                           rounded-md hover:bg-gray-200 hover:text-gray-800 transition">
                                Transfer owner
                            </button>

                            {{-- EDIT --}}
                            <a href="{{ route('dashboard.companies.edit', $company->id) }}"
                                class="text-sm text-gray-700 hover:underline">
                                Edit
                            </a>

                            {{-- DELETE --}}
                            <form action="{{ route('dashboard.companies.destroy', $company->id) }}"
                                method="POST"
                                class="inline"
                                onsubmit="return confirm('Are you sure?')">

                                @csrf
                                @method('DELETE')

                                <button class="text-sm text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    {{-- INACTIVE --}}
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">
            Inactive Companies
        </h3>

        <div class="bg-white border rounded-xl overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">

                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-left">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @foreach($inactiveCompanies as $company)

                    @php
                    $statusClass = match($company->status) {
                    'blocked' => 'bg-red-100 text-red-700',
                    'inactive' => 'bg-red-100 text-red-700',
                    'deleted' => 'bg-gray-300 text-gray-600',
                    default => 'bg-gray-100 text-gray-700',
                    };
                    @endphp

                    <tr class="bg-gray-50 opacity-80">

                        <td class="px-5 py-3 font-medium text-gray-700">
                            {{ $company->name }}
                        </td>

                        <td class="px-5 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                {{ $typeLabel($company->type) }}
                            </span>
                        </td>

                        <td class="px-5 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                {{ $company->status }}
                            </span>
                        </td>

                    </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ================= OWNER DRAWER ================= --}}
<div id="owner-overlay"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 transition-opacity">
</div>

<div id="owner-drawer"
    class="fixed right-0 top-0 h-full w-[460px] bg-white shadow-2xl
           transform translate-x-full transition-transform duration-300 z-50
           flex flex-col">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">
            Transfer Ownership
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Change the primary owner of this company. The new owner will gain full administrative control.
        </p>
    </div>

    {{-- FORM --}}
    <form id="owner-form" method="POST" class="flex flex-col flex-1">
        @csrf

        {{-- CONTENT --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

            <input type="hidden" name="user_id" id="owner-user-id">

            {{-- INFO BLOCK --}}
            <div class="p-4 rounded-lg bg-yellow-50 border border-yellow-100 text-sm text-yellow-800 leading-relaxed">
                <p class="mb-2 font-semibold">
                    What happens when you transfer ownership?
                </p>

                <ul class="list-disc pl-5 space-y-1">
                    <li>The selected user becomes the <b>primary owner</b></li>
                    <li>Your role will be downgraded to <b>member/admin</b> (depending on system rules)</li>
                    <li>Only the new owner can delete or fully manage the company</li>
                </ul>
            </div>

            <div>
                <label class="text-xs text-gray-500 uppercase tracking-wide">
                    New owner email
                </label>

                <input type="email"
                    name="email"
                    id="owner-email"
                    class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                    placeholder="example@email.com"
                    required>

                <p id="owner-email-result" class="text-xs mt-2 text-gray-500"></p>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between gap-2">

            <button type="button"
                onclick="closeOwnerDrawer()"
                class="px-4 py-2 text-sm rounded-lg border border-gray-200
                       text-gray-600 hover:bg-gray-50 transition">
                Cancel
            </button>

            <button type="submit"
                id="owner-confirm-transfer"
                class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                       hover:bg-gray-800 transition shadow-sm disabled:opacity-50"
                disabled>
                Confirm Transfer
            </button>

        </div>

    </form>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
    let selectedUserId = null;

    function openOwnerDrawer(companyId) {
           const confirmButton = document.getElementById('owner-confirm-transfer');
        document.getElementById('owner-form').action =
            `/dashboard/companies/${companyId}/transfer-owner`;

        document.getElementById('owner-overlay').classList.remove('hidden');
        document.getElementById('owner-drawer').classList.remove('translate-x-full');

        selectedUserId = null;
         confirmButton.disabled = true;
        document.getElementById('owner-email').value = '';
        document.getElementById('owner-email-result').innerHTML = '';
    }

    function closeOwnerDrawer() {
        document.getElementById('owner-overlay').classList.add('hidden');
        document.getElementById('owner-drawer').classList.add('translate-x-full');
    }

    document.getElementById('owner-overlay').addEventListener('click', closeOwnerDrawer);

    document.getElementById('owner-email').addEventListener('input', async function() {
        const email = this.value;
        const resultBox = document.getElementById('owner-email-result');
        const confirmButton = document.getElementById('owner-confirm-transfer');
        
        if (!email) return;

        const res = await fetch(`/dashboard/users/find-by-email?email=${encodeURIComponent(email)}`);
        const data = await res.json();

        if (!data.found) {
            resultBox.innerHTML = `<span class="text-red-600">Such Account Holder not found</span>`;
            selectedUserId = null;
            confirmButton.disabled = true;
            return;
        }

        confirmButton.disabled = false;

        resultBox.innerHTML = `
        <span class="text-green-600 font-medium">
            ${data.user.full_name ?? (data.user.name + ' ' + (data.user.last_name ?? ''))}
        </span>
        <span class="text-gray-400">(${data.user.email})</span>
    `;

        selectedUserId = data.user.id;
    });

    document.getElementById('owner-form').addEventListener('submit', function(e) {
        if (!selectedUserId) {
            e.preventDefault();
            alert('Please select a valid user by email');
            return;
        }

        document.getElementById('owner-user-id').value = selectedUserId;
    });
</script>

@endsection