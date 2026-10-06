<div
    x-data="{ open: false }"
    x-on:open-variations-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
>

    {{-- BACKDROP --}}
    <div
    class="absolute inset-0 bg-black/30 backdrop-blur-sm"
    @click="open = false"
></div>


    {{-- MODAL --}}
    <div class="relative z-10 flex min-h-full items-center justify-center p-4 sm:p-6">

        <div
            class="relative w-full max-w-5xl
                   max-h-[90vh]
                   overflow-y-auto
                   rounded-2xl
                   bg-white
                   shadow-2xl"
            @click.stop
        >

            {{-- CLOSE BUTTON --}}
            <button
                type="button"
                @click="open = false"
                class="absolute right-4 top-4 z-20
                       flex h-9 w-9 items-center justify-center
                       rounded-full
                       bg-white
                       text-gray-500
                       shadow-sm
                       transition
                       hover:bg-gray-100
                       hover:text-gray-900"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>

            </button>


            {{-- HEADER --}}
            <div class="border-b border-gray-200 px-6 py-5 pr-16">

                <h3 class="text-xl font-semibold text-gray-900">
                    Supplier's materials and color variations
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Available materials, colors and variations for this product.
                </p>

            </div>


            {{-- MATERIAL GROUPS --}}
            <div class="px-6 py-6">

                @if($product->materials->isNotEmpty())

                    @foreach($product->materials->groupBy('material_group_id') as $groupMaterials)

                        @php
                            $materialGroup = $groupMaterials->first()?->materialGroup;
                        @endphp

                        @if($materialGroup)

                            <div class="mb-8 last:mb-0">

                                {{-- GROUP HEADER --}}
                                <div class="flex items-start justify-between gap-6">

                                    {{-- GROUP NAME + DESCRIPTION --}}
                                    <div class="min-w-0 flex-1">

                                        <h4 class="text-sm font-semibold text-gray-900">
                                            {{ $materialGroup->translatedName() ?? $materialGroup->name ?? $materialGroup->slug }}
                                        </h4>

                                        @if($materialGroup->translatedDescription())

                                            <div class="mt-1 text-xs text-gray-500 leading-relaxed">
                                                {{ $materialGroup->translatedDescription() }}
                                            </div>

                                        @endif

                                    </div>


                                    {{-- BRAND + LOGO --}}
                                    @if($materialGroup->brand)

                                        <div class="shrink-0 flex flex-col items-center w-[80px]">

                                            {{-- BRAND LOGO --}}
                                            @if($materialGroup->logo?->cdn_url)

                                                <div
                                                    class="w-[80px] h-[80px]
                                                           flex items-center justify-center
                                                           overflow-hidden"
                                                >

                                                    @if($materialGroup->brand_url)

                                                        <a
                                                            href="{{ $materialGroup->brand_url }}"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="w-full h-full
                                                                   flex items-center justify-center"
                                                        >

                                                            <img
                                                                src="{{ $materialGroup->logo->cdn_url }}"
                                                                alt="{{ $materialGroup->brand }}"
                                                                class="w-full h-full object-contain"
                                                            >

                                                        </a>

                                                    @else

                                                        <img
                                                            src="{{ $materialGroup->logo->cdn_url }}"
                                                            alt="{{ $materialGroup->brand }}"
                                                            class="w-full h-full object-contain"
                                                        >

                                                    @endif

                                                </div>

                                            @endif

                                        </div>

                                    @endif

                                </div>


                                {{-- MATERIALS --}}
                                <div class="mt-3 flex flex-wrap gap-x-1 gap-y-3">

                                    @foreach($groupMaterials as $material)

                                        <div class="w-20 text-center">

                                            {{-- MATERIAL IMAGE --}}
                                            <div
                                                class="w-20 h-20
                                                       overflow-hidden
                                                       rounded
                                                       border
                                                       border-gray-200
                                                       bg-gray-50"
                                            >

                                                @if($material->photo?->cdn_url)

                                                    <img
                                                        src="{{ $material->photo->cdn_url }}"
                                                        alt="{{ $material->name }}"
                                                        class="w-full h-full object-cover"
                                                    >

                                                @else

                                                    <img
                                                        src="{{ asset('images/no-image.png') }}"
                                                        alt="{{ $material->name }}"
                                                        class="w-full h-full object-cover"
                                                    >

                                                @endif

                                            </div>


                                            {{-- MATERIAL NAME --}}
                                            <div
                                                class="mt-1
                                                       text-[10px]
                                                       font-medium
                                                       leading-tight
                                                       text-gray-800
                                                       text-center"
                                            >
                                                {{ $material->name }}
                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif

                    @endforeach

                @else

                    <div class="py-10 text-center text-sm text-gray-500">
                        No material variations available.
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>