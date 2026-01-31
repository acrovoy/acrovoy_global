@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('admin.currencies.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Back to currencies
        </a>

        <h2 class="text-2xl font-semibold mt-2">Exchange Rates</h2>
        <p class="text-sm text-gray-500">
            Manage exchange rates relative to the base currency (USD)
        </p>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded border border-green-200 bg-green-50 text-green-800 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white border rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr class="text-left text-gray-600">
                <th class="px-5 py-3 font-medium">Currency</th>
                <th class="px-5 py-3 font-medium">Name</th>
                <th class="px-5 py-3 font-medium">Rate to USD</th>
                <th class="px-5 py-3 font-medium text-right">Status</th>
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

                <td class="px-5 py-3">
                    @if($currency->code === 'USD')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                            1.0000 · Base currency
                        </span>
                    @else
                        <form method="POST"
                              action="{{ route('admin.exchange-rates.update', $currency) }}"
                              class="flex items-center gap-2">
                            @csrf
                            @method('PUT')

                            <input
                                type="number"
                                step="0.0001"
                                name="rate"
                                value="{{ $currency->rate->rate ?? '' }}"
                                class="border rounded-lg px-3 py-1.5 w-36 text-sm focus:ring-1 focus:ring-gray-400"
                                required
                            />

                            <button
                                class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs hover:bg-gray-800 transition">
                                Save
                            </button>
                        </form>
                    @endif
                </td>

                <td class="px-5 py-3 text-right">
                    @if($currency->code === 'USD')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-300 text-gray-700">
                            Locked
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                            Editable
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
