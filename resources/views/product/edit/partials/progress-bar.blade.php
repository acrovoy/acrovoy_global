@php
    $stepsConfig = [
        1 => ['title' => 'Basic'],
        2 => ['title' => 'Category'],
        3 => ['title' => 'Specs'],
        4 => ['title' => 'Gallery'],
        5 => ['title' => 'Pricing'],
        6 => ['title' => 'Shipping'],
        7 => ['title' => 'Variants'],
    ];

    $currentStep = (int) $steps;
    $productId = $product->id ?? null;
@endphp

<div class="mb-6">

    <div class="flex items-center justify-between gap-1 overflow-x-auto py-1">

        @foreach($stepsConfig as $stepNumber => $stepData)

            @php
                $isCompleted = $stepNumber < $currentStep;
                $isCurrent = $stepNumber === $currentStep;
            @endphp

            <div class="flex items-center flex-1 min-w-0">

               @if($mode === 'edit' && $productId)
    <a href="{{ route('supplier.products.edit-step', [
        'product' => $productId,
        'step' => $stepNumber
    ]) }}"
       class="group flex items-center gap-2 min-w-0">
@else
    <div class="group flex items-center gap-2 min-w-0 opacity-60 cursor-default">
@endif

                    {{-- CIRCLE --}}
                    <div
                        class="
                            w-7 h-7 rounded-full
                            flex items-center justify-center
                            text-[11px] font-semibold
                            shrink-0
                            transition-all duration-200

                            @if($isCurrent)
                                bg-black text-white ring-2 ring-gray-200
                            @elseif($isCompleted)
                                bg-green-600 text-white
                            @else
                                bg-gray-200 text-gray-600 group-hover:bg-gray-300
                            @endif
                        ">

                        @if($isCompleted)
                            ✓
                        @else
                            {{ $stepNumber }}
                        @endif

                    </div>

                    {{-- TEXT --}}
                    <div class="hidden lg:block min-w-0">

                        <div
                            class="
                                text-[11px] font-medium truncate

                                @if($isCurrent)
                                    text-black
                                @elseif($isCompleted)
                                    text-green-700
                                @else
                                    text-gray-500
                                @endif
                            ">

                            {{ $stepData['title'] }}

                        </div>

                    </div>

                </a>

                {{-- LINE --}}
                @if(!$loop->last)

                    <div class="flex-1 h-px mx-2
                        @if($isCompleted)
                            bg-green-500
                        @else
                            bg-gray-200
                        @endif">
                    </div>

                @endif

            </div>

        @endforeach

    </div>

</div>