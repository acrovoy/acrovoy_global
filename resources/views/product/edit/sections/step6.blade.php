
@include('product.edit.partials.progress-bar', [$mode = 'edit'])

<form method="POST" action="{{ route('supplier.products.update', ['product' => $product->id, 'step' => 6]) }}" enctype="multipart/form-data" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    <div x-data="{ customization: '{{ old('customization', $product->customization ? 1 : 0) }}' }">

        {{-- CUSTOMIZATION --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Customization</label>

            <select
                name="customization"
                x-model="customization"
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-black focus:ring-2 focus:ring-black/10 transition"
            >
                <option value="1">Customization Available</option>
                <option value="0">No Customization</option>
            </select>
        </div>

        {{-- MATERIALS --}}
        <div x-show="customization === '1'" x-collapse>

            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Materials available for this product</h3>
                <p class="mt-1 text-sm text-gray-500">Select the materials and finishes available for this product.</p>
            </div>

            {{-- SELECTED --}}
            <div id="selected-materials" class="flex flex-wrap gap-2 mb-4"></div>

            {{-- SEARCH --}}
            <div class="relative mb-8">
                <input
                    type="text"
                    id="materialSearch"
                    placeholder="Search materials..."
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pr-10 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition"
                >

                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" d="m20 20-4-4"/>
                    </svg>
                </div>
            </div>

            @php
                $groupedMaterials = collect($materialsPrepared)->groupBy(fn ($material) => $material['group']['id'] ?? 0);

                $supplierMaterialGroups = $groupedMaterials->filter(
                    fn ($materials) => (int) ($materials->first()['group']['is_custom'] ?? 0) === 1
                );

                $platformMaterialGroups = $groupedMaterials->filter(
                    fn ($materials) => (int) ($materials->first()['group']['is_custom'] ?? 0) === 0
                );
            @endphp

            <div id="materials-options">

                {{-- YOUR MATERIALS --}}
                @if($supplierMaterialGroups->isNotEmpty())
                    <section data-material-section="supplier" class="mb-10">

                        <div class="mb-5 flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-900 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7.5v9a2 2 0 0 1-1 1.732l-7 4.041a2 2 0 0 1-2 0l-7-4.041A2 2 0 0 1 2 16.5v-9a2 2 0 0 1 1-1.732l7-4.041a2 2 0 0 1 2 0l7 4.041A2 2 0 0 1 20 7.5Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.5 6.5 9.5 5.5 9.5-5.5M12 22V12"/>
                                </svg>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-semibold text-gray-900">Your Materials</h3>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">Supplier</span>
                                </div>
                                <p class="mt-1 text-xs leading-relaxed text-gray-500">Materials and finishes provided by your company.</p>
                            </div>
                        </div>

                        @foreach($supplierMaterialGroups as $groupId => $groupMaterials)
                            @php $materialGroup = $groupMaterials->first()['group'] ?? null; @endphp

                            <div class="mb-8 material-group" data-group-id="{{ $groupId }}">
                                @if($materialGroup)
                                    <h4 class="mb-1 text-sm font-semibold text-gray-900">{{ $materialGroup['name'] }}</h4>

                                    @if(!empty($materialGroup['description']))
                                        <div class="mb-3 max-w-3xl text-xs leading-relaxed text-gray-500">{{ $materialGroup['description'] }}</div>
                                    @else
                                        <div class="mb-2"></div>
                                    @endif
                                @endif

                                <div class="grid grid-cols-[repeat(auto-fill,80px)] gap-x-3 gap-y-4">
                                    @foreach($groupMaterials as $material)
                                        @php
                                            $materialName = $material['translations'][app()->getLocale()]['name']
                                                ?? $material['translations']['en']['name']
                                                ?? '';
                                            $materialPhoto = $material['photo']['cdn_url'] ?? null;
                                        @endphp

                                        <button type="button" class="material-option group w-20 text-left" data-id="{{ $material['id'] }}" data-name="{{ $materialName }}">
                                            <div class="h-20 w-20 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 transition-all duration-200 group-hover:border-gray-400 group-hover:shadow-sm">
                                                <img
                                                    src="{{ $materialPhoto ?? asset('images/no-image.png') }}"
                                                    alt="{{ $materialName }}"
                                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                                                >
                                            </div>

                                            <div class="mt-1 w-20 text-center text-[10px] font-medium leading-tight text-gray-900">
                                                {{ $materialName }}
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif

                {{-- DIVIDER --}}
                @if($supplierMaterialGroups->isNotEmpty() && $platformMaterialGroups->isNotEmpty())
                    <div class="relative my-10">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>

                        <div class="relative flex justify-center">
                            <span class="bg-white px-4 text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                                Acrovoy Platform
                            </span>
                        </div>
                    </div>
                @endif

                {{-- ACROVOY MATERIALS --}}
                @if($platformMaterialGroups->isNotEmpty())
                    <section data-material-section="platform" class="mb-8">

                        <div class="mb-5 flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8.5"/>
                                    <path stroke-linecap="round" d="M12 8v8M8 12h8"/>
                                </svg>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-semibold text-gray-900">Acrovoy Catalog</h3>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">Platform</span>
                                </div>

                                <p class="mt-1 text-xs leading-relaxed text-gray-500">Standard materials available across the Acrovoy platform.</p>
                            </div>
                        </div>

                        @foreach($platformMaterialGroups as $groupId => $groupMaterials)
                            @php $materialGroup = $groupMaterials->first()['group'] ?? null; @endphp

                            <div class="mb-8 material-group" data-group-id="{{ $groupId }}">
                                @if($materialGroup)
                                    <h4 class="mb-1 text-sm font-semibold text-gray-900">{{ $materialGroup['name'] }}</h4>

                                    @if(!empty($materialGroup['description']))
                                        <div class="mb-3 max-w-3xl text-xs leading-relaxed text-gray-500">{{ $materialGroup['description'] }}</div>
                                    @else
                                        <div class="mb-2"></div>
                                    @endif
                                @endif

                                <div class="grid grid-cols-[repeat(auto-fill,80px)] gap-x-3 gap-y-4">
                                    @foreach($groupMaterials as $material)
                                        @php
                                            $materialName = $material['translations'][app()->getLocale()]['name']
                                                ?? $material['translations']['en']['name']
                                                ?? '';
                                            $materialPhoto = $material['photo']['cdn_url'] ?? null;
                                        @endphp

                                        <button type="button" class="material-option group w-20 text-left" data-id="{{ $material['id'] }}" data-name="{{ $materialName }}">
                                            <div class="h-20 w-20 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 transition-all duration-200 group-hover:border-gray-400 group-hover:shadow-sm">
                                                <img
                                                    src="{{ $materialPhoto ?? asset('images/no-image.png') }}"
                                                    alt="{{ $materialName }}"
                                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                                                >
                                            </div>

                                            <div class="mt-1 w-20 text-center text-[10px] font-medium leading-tight text-gray-900">
                                                {{ $materialName }}
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif


                {{-- PAGINATION --}}
                <div
                    id="materials-pagination"
                    class="mt-8 flex items-center justify-center gap-2"
                ></div>




            </div>

            {{-- SELECTED MATERIALS INPUT --}}
            <input
                type="hidden"
                name="materials_selected"
                id="materialsSelectedInput"
                value="{{ implode(',', $product->materials->pluck('id')->toArray()) }}"
            >
        </div>

        {{-- NAVIGATION --}}
        <div class="flex justify-between">
            <a
                href="{{ route('supplier.products.edit-step', [$product->id, 2]) }}"
                class="mt-4 rounded border border-gray-400 bg-gray-50 px-6 py-2 text-gray-400 hover:bg-gray-100"
            >
                Previous
            </a>

            <button
                type="submit"
                class="mt-4 rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-500"
            >
                Next
            </button>
        </div>

    </div>
</form>

