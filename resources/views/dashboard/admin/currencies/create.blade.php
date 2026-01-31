@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6 max-w-3xl">

    {{-- Errors --}}
    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">
            Add Currency
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Create a new currency and configure its basic parameters
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <form method="POST"
              action="{{ route('admin.currencies.store') }}"
              class="p-6 flex flex-col gap-6">
            @csrf

            @include('dashboard.admin.currencies._form')

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.currencies.index') }}"
                   class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>

                <button
                    class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                    Save currency
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
