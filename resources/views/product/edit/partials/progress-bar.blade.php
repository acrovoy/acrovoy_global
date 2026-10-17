
@php
    $stepsConfig = [
        1 => ['title' => 'Basic'],
        2 => ['title' => 'Category & Attributes'],
        3 => ['title' => 'Custom Specifications'],
        4 => ['title' => 'Gallery'],
        5 => ['title' => 'Pricing'],
        6 => ['title' => 'Customization'],
        7 => ['title' => 'Variants'],
        8 => ['title' => 'Shipping & Payments'],
    ];

    $currentStep = (int) $steps;
    $productId = $product->id ?? null;
@endphp

<div class="mb-4 rounded-lg border border-gray-200 bg-white px-3 py-3">

    <div class="grid grid-cols-2 gap-x-3 gap-y-3 sm:grid-cols-4">

        @foreach($stepsConfig as $stepNumber => $stepData)

            @php
                $isCompleted = $stepNumber < $currentStep;
                $isCurrent = $stepNumber === $currentStep;

                $column = ($stepNumber - 1) % 4;
                $isLastInRow = $column === 3;
            @endphp

            <div class="flex min-w-0 items-center">

                {{-- STEP LINK --}}
                @if($mode === 'edit' && $productId)
                    <a
                        href="{{ route('supplier.products.edit-step', [
                            'product' => $productId,
                            'step' => $stepNumber,
                        ]) }}"
                        class="flex min-w-0 shrink-0 items-center gap-2 rounded-md py-1 transition hover:bg-gray-50"
                    >
                @else
                    <div class="flex min-w-0 shrink-0 items-center gap-2 py-1 opacity-60">
                @endif

                    {{-- CIRCLE --}}
                    <div class="
                        flex h-7 w-7 shrink-0 items-center justify-center
                        rounded-full border text-[11px] font-semibold
                        transition-colors duration-200
                        @if($isCurrent)
                            border-black bg-black text-white ring-2 ring-gray-200
                        @elseif($isCompleted)
                            border-green-600 bg-green-600 text-white
                        @else
                            border-gray-200 bg-gray-100 text-gray-500
                        @endif
                    ">
                        @if($isCompleted)
                            ✓
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>

                    {{-- TITLE --}}
                    <span class="
                        whitespace-nowrap text-xs leading-tight
                        @if($isCurrent)
                            font-semibold text-gray-900
                        @elseif($isCompleted)
                            font-medium text-green-700
                        @else
                            font-medium text-gray-500
                        @endif
                    ">
                        {{ $stepData['title'] }}
                    </span>

                @if($mode === 'edit' && $productId)
                    </a>
                @else
                    </div>
                @endif

                {{-- CONNECTOR --}}
                @if(!$isLastInRow)
                    <div class="
                        mx-2 hidden h-px min-w-2 flex-1 sm:block
                        {{ $isCompleted ? 'bg-green-500' : 'bg-gray-200' }}
                    "></div>
                @endif

            </div>

        @endforeach

    </div>

   

</div>
