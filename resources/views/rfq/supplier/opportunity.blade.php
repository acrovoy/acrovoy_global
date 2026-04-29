@extends('dashboard.layout')

@section('dashboard-content')

@include('rfq.partials.header', [
    'rfq' => $rfq,
    'isSupplier' => true,
    'isBuyer' => false
])

<form method="POST" action="#">
@csrf

<div class="space-y-6">

   

    {{-- ========================= --}}
    {{-- REQUIREMENTS --}}
    {{-- ========================= --}}
    <div class="bg-white p-6">

        <h3 class="font-semibold text-gray-900 mb-4">
            Requirements
        </h3>

        @if($attributes->isEmpty())

            <div class="text-sm text-gray-500">
                No requirements defined
            </div>

        @else

            <div class="space-y-4">

                @foreach($attributes as $req)

                    @php
                        $type = $req->type ?? null;
                        $options = $req->options ?? collect();
                    @endphp

                    <div class="border rounded-lg p-4 space-y-3">

                        {{-- HEADER --}}
                        <div class="flex justify-between">

                            <div class="text-sm font-medium text-gray-900">
                                {{ $req->name }}
                            </div>

                            

                        </div>

                        {{-- SPEC --}}
                        @if($req->value_text)
                            <div class="text-sm text-gray-600">
                                {{ $req->value_text }}
                            </div>
                        @endif

                        

                        {{-- ========================= --}}
                        {{-- SELECT --}}
                        {{-- ========================= --}}
                        @if($type === 'select')

                            <div class="mt-2">

                                <select name="items[{{ $req->id }}][value]"
                                        class="border rounded px-3 py-2 text-sm w-full">

                                    <option value="">Select option</option>

                                    @foreach($options as $option)

                                        <option value="{{ $option->id }}"
                                            @selected(($req->selected_option_id ?? null) == $option->id)
                                        >
                                            {{ $option->translatedValue() ?? $option->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        @endif

                        {{-- ========================= --}}
                        {{-- MULTISELECT --}}
                        {{-- ========================= --}}
                        @if($type === 'multiselect')

                            <div class="mt-2 space-y-2">

                                <input type="hidden"
                                       name="items[{{ $req->id }}][value]"
                                       value="">

                                @foreach($options as $option)

                                    <label class="flex items-center gap-2 text-sm text-gray-700">

                                        <input type="checkbox"
                                               name="items[{{ $req->id }}][value][]"
                                               value="{{ $option->id }}"
                                               @checked(in_array($option->id, $req->selected_options ?? []))
                                        >

                                        <span>
                                            {{ $option->translatedValue() ?? $option->name }}
                                        </span>

                                    </label>

                                @endforeach

                            </div>

                        @endif


                        {{-- INPUTS --}}
                        

                        <textarea name="items[{{ $req->id }}][notes]"
                                  class="border rounded px-3 py-2 text-sm w-full"
                                  placeholder="Notes"></textarea>

                                  <div class="grid grid-cols-3 gap-3 mt-2">

                            <input type="number"
                                   name="items[{{ $req->id }}][price]"
                                   class="border rounded px-3 py-2 text-sm"
                                   placeholder="Price">

                            <input type="number"
                                   name="items[{{ $req->id }}][quantity]"
                                   class="border rounded px-3 py-2 text-sm"
                                   placeholder="Qty">

                            <input type="number"
                                   name="items[{{ $req->id }}][lead_time]"
                                   class="border rounded px-3 py-2 text-sm"
                                   placeholder="Lead time">

                        </div>



                    </div>

                @endforeach

            </div>

        @endif

    </div>

    {{-- ========================= --}}
    {{-- OFFER SUMMARY --}}
    {{-- ========================= --}}
    <div class="bg-white border rounded-xl p-6">

        <h3 class="font-semibold text-gray-900 mb-4">
            Offer Summary
        </h3>

        <div class="text-sm text-gray-500 space-y-2">

            <div class="flex justify-between">
                <span>Total price</span>
                <span class="font-semibold">$0.00</span>
            </div>

            <div class="flex justify-between">
                <span>Avg lead time</span>
                <span class="font-semibold">-</span>
            </div>

        </div>

    </div>

    {{-- ========================= --}}
    {{-- ACTIONS --}}
    {{-- ========================= --}}
    <div class="flex justify-end gap-2">

        <button type="submit"
                class="px-4 py-2 border rounded-lg text-sm">
            Save Draft
        </button>

        <button type="submit"
                class="px-4 py-2 bg-black text-white rounded-lg text-sm">
            Submit Offer
        </button>

    </div>

</div>

</form>

@endsection