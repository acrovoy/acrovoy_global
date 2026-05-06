@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-5xl mx-auto">

    {{-- RFQ LIST --}}
    @for($i = 1; $i <= 5; $i++)

        @php
            $isActive = $i === 2; // просто для примера открыт второй
        @endphp

        <div class="border rounded-lg mb-3 overflow-hidden">

            {{-- HEADER --}}
            <div class="flex justify-between items-center p-3 bg-gray-50">

                <div class="flex items-center gap-3">
                    <div class="w-5 font-semibold">
                        {{ $i }}
                    </div>

                    <img src="https://via.placeholder.com/40"
                         class="w-10 h-10 rounded object-cover">

                    <div class="text-sm">
                        Новая модель стола для производства
                    </div>
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <span class="px-2 py-1 bg-gray-200 rounded">
                        {{ $i == 1 ? 'Published' : 'Draft' }}
                    </span>

                    <span class="text-red-500 text-xs">
                        {{ $i == 1 ? 'Awaiting your reply for supplier\'s offer' : 'Offer submitted to buyer' }}
                    </span>
                </div>
            </div>

            {{-- BODY --}}
            @if($isActive)

                <div class="p-5 bg-white">

                    {{-- ACTIONS --}}
                    <div class="flex justify-end gap-3 mb-4">
                        <button class="px-4 py-1 border rounded">
                            Chat
                        </button>

                        <button class="px-4 py-1 border rounded">
                            Save Draft
                        </button>

                        <button class="px-4 py-1 bg-black text-white rounded">
                            Submit Offer
                        </button>
                    </div>

                    {{-- REQUIREMENT --}}
                    <div class="border rounded-lg p-4 mb-6">

                        <div class="font-medium mb-2">
                            General conditions
                        </div>

                        {{-- TEXT --}}
                        <textarea class="w-full border rounded p-2 mb-4"
                                  placeholder="Notes"></textarea>

                        {{-- IMAGE + PRICE --}}
                        <div class="flex items-center gap-3 mb-4">
                            <img src="https://via.placeholder.com/50" class="w-12 h-12 rounded">

                            <div class="w-12 h-12 border-dashed border rounded flex items-center justify-center">
                                +
                            </div>

                            <input type="text"
                                   class="ml-auto border rounded px-3 py-1 w-32"
                                   value="$ 50.00">
                        </div>

                        {{-- RADIO --}}
                        <div class="mb-4">
                            <div class="text-sm mb-1">Форма</div>

                            <label class="mr-4">
                                <input type="radio"> Металл
                            </label>

                            <label>
                                <input type="radio" checked> Ротанг
                            </label>
                        </div>

                        <textarea class="w-full border rounded p-2 mb-4"
                                  placeholder="Notes"></textarea>

                        {{-- MATERIAL CHECKBOX --}}
                        <div class="mb-4">
                            <div class="text-sm mb-2">Материал</div>

                            @for($j = 0; $j < 8; $j++)
                                <label class="mr-3">
                                    <input type="checkbox" {{ $j > 3 ? 'checked' : '' }}>
                                    Ротанг
                                </label>
                            @endfor
                        </div>

                        <textarea class="w-full border rounded p-2 mb-4"
                                  placeholder="Notes"></textarea>

                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 border-dashed border rounded flex items-center justify-center">
                                +
                            </div>

                            <input type="text"
                                   class="ml-auto border rounded px-3 py-1 w-32"
                                   placeholder="Price">
                        </div>

                    </div>

                    {{-- ATTACHMENTS --}}
                    <div class="border rounded-lg p-4 mb-6">
                        <div class="font-medium mb-2">Attachments</div>

                        <div class="text-xs text-gray-500 mb-3">
                            Upload relevant files including technical drawings...
                        </div>

                        <div class="flex items-center gap-3">
                            <img src="https://via.placeholder.com/60" class="w-14 h-14 rounded">

                            <div class="w-14 h-14 border-dashed border rounded flex items-center justify-center">
                                +
                            </div>
                        </div>
                    </div>

                    {{-- DELIVERY --}}
                    <div>
                        <div class="font-medium mb-3">Delivery Services</div>

                        <div class="flex gap-2 mb-4">
                            <input class="border p-2 rounded w-full"
                                   value="Buenos Aires, Argentina">

                            <input class="border p-2 rounded w-full"
                                   value="Buenos Aires, Argentina">
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            @for($k = 0; $k < 2; $k++)
                                <div class="border rounded-lg p-4">

                                    <div class="font-medium mb-1">
                                        Delivery by Acrovoy
                                    </div>

                                    <div class="text-sm text-gray-500 mb-3">
                                        Delivery handled by platform
                                    </div>

                                    <div class="bg-blue-100 text-blue-700 px-3 py-2 rounded w-fit">
                                        Price: $0.00
                                    </div>

                                </div>
                            @endfor

                        </div>
                    </div>

                </div>

            @endif

        </div>

    @endfor

</div>

@endsection