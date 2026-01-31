@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Currencies</h2>
            <p class="text-sm text-gray-500">
                Manage available currencies, priority and default settings
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.exchange-rates.index') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                Manage exchange rates
            </a>

            <a href="{{ route('admin.currencies.create') }}"
               class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                + Add currency
            </a>
        </div>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Code</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Name</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Symbol</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Active</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Priority</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Default</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-600">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($currencies as $currency)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-semibold text-gray-900">
                            {{ $currency->code }}
                        </td>

                        <td class="px-5 py-3 text-gray-700">
                            {{ $currency->name }}
                        </td>

                        <td class="px-5 py-3 text-gray-700">
                            {{ $currency->symbol ?? '—' }}
                        </td>

                        {{-- Active --}}
                        <td class="px-5 py-3">
                            @if($currency->is_active)
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Priority --}}
                        <td class="px-5 py-3">
                            @if($currency->is_priority)
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    Yes
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">
                                    No
                                </span>
                            @endif
                        </td>

                        {{-- Default --}}
                        <td class="px-5 py-3">
                            @if($currency->is_default)
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-900 text-white">
                                    Default
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">
                                    —
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.currencies.edit', $currency) }}"
                               class="text-sm text-gray-700 hover:underline mr-3">
                                Edit
                            </a>

                            <form action="{{ route('admin.currencies.destroy', $currency) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete currency?')">
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
@endsection
