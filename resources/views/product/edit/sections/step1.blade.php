{{-- ================= PROGRESS BAR ================= --}}


@include('product.edit.partials.progress-bar', [$mode = 'edit'])



<form method="POST"
            action="{{ route('supplier.products.update', [
          'product' => $product->id,
          'step' => 1
      ]) }}"
            enctype="multipart/form-data"
            class="" id="productForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

{{-- ================= BASIC INFO ================= --}}

<h3 class="text-2xl font-bold mb-6">Basic Information</h3>

{{-- ================= Product Name ================= --}}
<div x-data="{ open: false }" class="border rounded p-4 mb-4 bg-white shadow-sm">
    <h4 class="font-semibold mb-3">Product Name

    <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">Название товара</div>
                <div class="text-gray-200 text-sm">
                    Укажите понятное и читаемое название товара, которое будет
                    отображаться покупателям в каталоге и поиске.
                </div>
                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>используйте общепринятые термины (стул, стол, барная табуретка)</li>
                    <li>не добавляйте внутренние коды поставщика</li>
                    <li>избегайте лишних символов и сокращений</li>
                    <li>максимальное количество символов: <span class="font-semibold text-white">110</span></li>
                </ul>
                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Пример: <span class="text-gray-200">Барный стул из дуба, 75 см</span> или <span class="text-gray-200">Стул для столовой, металлический каркас, черный</span>
                </div>
            </div>
        </x-help-tooltip>


    </h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
            @php
                $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');
            @endphp

            @if($index == 0)
                {{-- Первый язык всегда виден --}}
                <div class="flex-1 flex items-center gap-2">
                    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">

                    <x-char-counter :max="110">
                        <div x-data="charCounter(110)" class="relative w-full">
                            <input type="text"
                                   name="name[{{ $language->code }}]"
                                   class="input mb-2 w-full"
                                   maxlength="110"
                                   placeholder="Product Name (required)"
                                   value="{{ old('name.' . $language->code, $translations[$language->code]['name'] ?? '') }}"
                                   @input="update($event.target)"
                                   x-init="update($el)"
                                   required
                                   style="padding-right: 4rem;">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs" :class="color">
                                <span x-text="count"></span>/<span x-text="max"></span>
                            </div>
                        </div>
                    </x-char-counter>

                    
                </div>
            @else
               {{-- Остальные языки скрыты по умолчанию --}}
<div x-show="open" x-collapse x-cloak class="flex-1 flex items-center gap-2 mb-2">
    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">

    <x-char-counter :max="110">
        <div x-data="charCounter(110)" class="relative w-full">
            <input type="text"
                   name="name[{{ $language->code }}]"
                   maxlength="110"
                   class="input w-full"
                   placeholder="Product Name (optional)"
                   value="{{ old('name.' . $language->code, $translations[$language->code]['name'] ?? '') }}"
                   @input="update($event.target)"
                   x-init="update($el)"
                   style="padding-right: 4rem;">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                 :class="color">
                <span x-text="count"></span>/<span x-text="max"></span>
            </div>
        </div>
    </x-char-counter>
</div>
            @endif
        @endforeach
    </div>

    {{-- Кнопка для остальных языков --}}
    @if(count($languages) > 1)
    <div class="flex justify-between">
        <div class="mt-1 text-xs text-red-500 italic">
            * English version required
        </div>
        <button type="button"
                @click="open = !open"
                class="mt-2 text-xs text-blue-600 hover:underline flex items-center gap-1">
            Other Languages
            <svg :class="{ 'rotate-180': open }"
                 class="w-4 h-4 transition-transform"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        </div>
    @endif
</div>

{{-- ================= Short Description ================= --}}
<div x-data="{ open: false }" class="border rounded p-4 mb-4 bg-white shadow-sm">
    <h4 class="font-semibold mb-3">Short Undername Description

    <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">

                <div class="font-semibold text-white">
                    Краткое описание товара
                </div>

                <div class="text-gray-200 text-sm">
                    Используется как короткий подзаголовок товара рядом с названием
                    в карточке товара и каталоге.
                </div>

                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>укажите ключевое преимущество товара</li>
                    <li>можно добавить материал или назначение</li>
                    <li>должно быть коротко и понятно</li>
                    <li>максимум: <span class="font-semibold text-white">60 символов</span></li>
                </ul>

                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Пример:
                    <span class="text-gray-200">Металлический каркас, штабелируемый</span>
                </div>

            </div>
        </x-help-tooltip>
    </h4>


    </h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
            @php
                $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');
            @endphp

            @if($index == 0)
                <div class="flex-1 flex items-center gap-2">
                    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">

                    <x-char-counter :max="60">

                        <div x-data="charCounter(60)"
                             class="relative w-full">

                            <input type="text"
                                   name="undername[{{ $language->code }}]"
                                   maxlength="60"
                                   class="input mb-2 w-full"
                                   placeholder="Short Description ({{ $language->code }})"
                                   value="{{ old('undername.' . $language->code, $translations[$language->code]['undername'] ?? '') }}"
                                   @input="update($event.target)"
                                   x-init="update($el)"
                                   style="padding-right: 4rem;">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                                 :class="color">

                                <span x-text="count"></span>/<span x-text="max"></span>

                            </div>

                        </div>

                    </x-char-counter>

                    
                </div>
            @else
                <div x-show="open"
                     x-collapse
                     class="flex-1 flex items-center gap-2 mb-2">
                    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">

                    <x-char-counter :max="60">

                        <div x-data="charCounter(60)"
                             class="relative w-full">

                            <input type="text"
                                   name="undername[{{ $language->code }}]"
                                   maxlength="60"
                                   class="input w-full"
                                   placeholder="Short Description (optional)"
                                   value="{{ old('undername.' . $language->code, $translations[$language->code]['undername'] ?? '') }}"
                                   @input="update($event.target)"
                                   x-init="update($el)"
                                   style="padding-right: 4rem;">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                                 :class="color">

                                <span x-text="count"></span>/<span x-text="max"></span>

                            </div>

                        </div>

                    </x-char-counter>

                    
                </div>
            @endif
        @endforeach
    </div>

    @if(count($languages) > 1)
    <div class="flex justify-between mt-1">

        <div class="text-xs text-gray-500 italic">
            * Optional, maximum 60 characters
        </div>

        <button type="button"
                @click="open = !open"
                class="mt-2 text-xs text-blue-600 hover:underline flex items-center gap-1">
            Other Languages
            <svg :class="{ 'rotate-180': open }"
                 class="w-4 h-4 transition-transform"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        </div>
    @endif
</div>



{{-- ================= Full Product Description ================= --}}
<div
    x-data="productDescriptionEditor()"
    class="border rounded p-4 mb-4 bg-white shadow-sm"
>
    <h4 class="font-semibold mb-3">
        Full Product Description

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">

                <div class="font-semibold text-white">
                    Full Product Description
                </div>

                <div class="text-gray-200 text-sm">
                    Provide a detailed description of the product.
                    Explain materials, construction, usage, and key benefits.
                </div>

                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>describe materials and build quality</li>
                    <li>mention dimensions or compatibility if relevant</li>
                    <li>explain where the product can be used</li>
                    <li>highlight durability or commercial suitability</li>
                    <li>avoid supplier codes or duplicated title text</li>
                    <li>
                        maximum length:
                        <span class="font-semibold text-white">
                            2000 characters
                        </span>
                    </li>
                </ul>

                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Example:
                    <span class="text-gray-200">
                        This commercial-grade dining chair features a reinforced steel frame,
                        powder-coated finish, and ergonomic seat designed for intensive use
                        in restaurants, cafés, and hospitality interiors.
                    </span>
                </div>

            </div>
        </x-help-tooltip>
    </h4>

    <div class="flex-col md:flex-row gap-2">

        @foreach($languages as $index => $language)

            @php
                $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');

                $description = old(
                    'description.' . $language->code,
                    $translations[$language->code]['description'] ?? ''
                );
            @endphp

            @if($index == 0)

                {{-- =====================================================
                    DEFAULT LANGUAGE
                ====================================================== --}}
                <div class="flex-1 flex items-start gap-2">

                    <img
                        src="{{ $flagPath }}"
                        alt="{{ $language->code }}"
                        class="w-5 h-5 rounded mt-3"
                    >

                    <div class="flex-1">

                        {{-- Toolbar --}}
                        <div class="flex items-center gap-1 border border-gray-300 border-b-0 rounded-t-lg bg-gray-50 px-2 py-1">

                            {{-- Bold --}}
                            <button
                                type="button"
                                @click="format('bold', '{{ $language->code }}')"
                                class="w-8 h-8 flex items-center justify-center rounded text-sm font-bold text-gray-700 hover:bg-gray-200"
                                title="Bold"
                            >
                                B
                            </button>

                            {{-- Bullet list --}}
                            <button
                                type="button"
                                @click="format('insertUnorderedList', '{{ $language->code }}')"
                                class="w-8 h-8 flex items-center justify-center rounded text-gray-700 hover:bg-gray-200"
                                title="Bullet list"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"
                                    />
                                </svg>
                            </button>

                        </div>

                        {{-- Editor --}}
                        <div
                            id="description-editor-{{ $language->code }}"
                            contenteditable="true"
                            data-language="{{ $language->code }}"
                            class="description-editor w-full min-h-[130px] border border-gray-300 rounded-b-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-400"
                            style="padding-right:4rem;"
                            @input="sync('{{ $language->code }}')"
                        >{!! $description !!}</div>

                        {{-- Hidden textarea submitted to Laravel --}}
                        <textarea
                            name="description[{{ $language->code }}]"
                            id="description-input-{{ $language->code }}"
                            class="hidden"
                            maxlength="2000"
                        >{{ $description }}</textarea>

                        {{-- Counter --}}
                        <div class="flex justify-end mt-1">
                            <div
                                class="text-xs"
                                id="description-counter-{{ $language->code }}"
                            >
                                <span>0</span>/2000
                            </div>
                        </div>

                    </div>

                </div>

            @else

                {{-- =====================================================
                    OTHER LANGUAGES
                ====================================================== --}}
                <div
                    x-show="open"
                    x-collapse
                    class="flex-1 flex items-start gap-2 mb-2"
                >

                    <img
                        src="{{ $flagPath }}"
                        alt="{{ $language->code }}"
                        class="w-5 h-5 rounded mt-3"
                    >

                    <div class="flex-1">

                        {{-- Toolbar --}}
                        <div class="flex items-center gap-1 border border-gray-300 border-b-0 rounded-t-lg bg-gray-50 px-2 py-1">

                            {{-- Bold --}}
                            <button
                                type="button"
                                @click="format('bold', '{{ $language->code }}')"
                                class="w-8 h-8 flex items-center justify-center rounded text-sm font-bold text-gray-700 hover:bg-gray-200"
                                title="Bold"
                            >
                                B
                            </button>

                            {{-- Bullet list --}}
                            <button
                                type="button"
                                @click="format('insertUnorderedList', '{{ $language->code }}')"
                                class="w-8 h-8 flex items-center justify-center rounded text-gray-700 hover:bg-gray-200"
                                title="Bullet list"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"
                                    />
                                </svg>
                            </button>

                        </div>

                        {{-- Editor --}}
                        <div
                            id="description-editor-{{ $language->code }}"
                            contenteditable="true"
                            data-language="{{ $language->code }}"
                            class="description-editor w-full min-h-[130px] border border-gray-300 rounded-b-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-400"
                            @input="sync('{{ $language->code }}')"
                        >{!! $description !!}</div>

                        {{-- Hidden textarea --}}
                        <textarea
                            name="description[{{ $language->code }}]"
                            id="description-input-{{ $language->code }}"
                            class="hidden"
                            maxlength="2000"
                        >{{ $description }}</textarea>

                        {{-- Counter --}}
                        <div class="flex justify-end mt-1">
                            <div
                                class="text-xs"
                                id="description-counter-{{ $language->code }}"
                            >
                                <span>0</span>/2000
                            </div>
                        </div>

                    </div>

                </div>

            @endif

        @endforeach

    </div>

    @if(count($languages) > 1)

        <div class="flex justify-between mt-1">

            <div class="text-xs text-red-500 italic">
                * English version required
            </div>

            <button
                type="button"
                @click="open = !open"
                class="mt-2 text-xs text-blue-600 hover:underline flex items-center gap-1"
            >
                Other Languages

                <svg
                    :class="{ 'rotate-180': open }"
                    class="w-4 h-4 transition-transform"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </button>

        </div>

    @endif
</div>


{{-- =========================================================
    DESCRIPTION EDITOR
========================================================= --}}
<script>
function productDescriptionEditor() {
    return {
        open: false,

        init() {
            document.querySelectorAll('.description-editor').forEach(editor => {
                const language = editor.dataset.language;

                this.sync(language);
            });
        },

        format(command, language) {
            const editor = document.getElementById(
                'description-editor-' + language
            );

            if (!editor) {
                return;
            }

            editor.focus();

            document.execCommand(command, false, null);

            this.sync(language);
        },

        sync(language) {
            const editor = document.getElementById(
                'description-editor-' + language
            );

            const input = document.getElementById(
                'description-input-' + language
            );

            const counter = document.getElementById(
                'description-counter-' + language
            );

            if (!editor || !input) {
                return;
            }

            input.value = editor.innerHTML;

            const text = editor.innerText
                .replace(/\u00a0/g, ' ')
                .trim();

            const count = text.length;

            if (counter) {
                const countElement = counter.querySelector('span');

                if (countElement) {
                    countElement.textContent = count;
                }

                counter.classList.toggle(
                    'text-red-500',
                    count > 2000
                );

                counter.classList.toggle(
                    'text-gray-400',
                    count <= 2000
                );
            }
        }
    }
}
</script>


<style>
.description-editor ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}

.description-editor ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}

.description-editor li {
    margin: 0.15rem 0;
}

.description-editor strong,
.description-editor b {
    font-weight: 700;
}

.description-editor:empty:before {
    content: "Full Description...";
    color: #9ca3af;
    pointer-events: none;
}
</style>










{{-- SKU --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm">

    <h4 class="font-semibold mb-3 flex items-center gap-2">

        SKU

        <x-help-tooltip width="w-80">

            <div class="space-y-2 leading-relaxed">

                <div class="font-semibold text-white">
                    SKU (Stock Keeping Unit)
                </div>

                <div class="text-gray-200 text-sm">
                    Unique product identifier used for inventory tracking,
                    integrations, and internal product management.
                </div>

                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>use only letters, numbers, and hyphens</li>
                    <li>do not use spaces or special characters</li>
                    <li>each SKU must be unique</li>
                    <li>maximum length: <span class="text-white font-semibold">64 characters</span></li>
                </ul>

                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Example:
                    <span class="text-gray-200">
                        CHR-OAK-75-BLK
                    </span>
                </div>

            </div>

        </x-help-tooltip>

    </h4>


    <x-char-counter :max="64">

        <div x-data="charCounter(64)"
             class="relative w-full">

            <input
                type="text"
                name="sku"
                maxlength="64"
                required
                class="input w-full"
                placeholder="Enter SKU"
                value="{{ old('sku', $product->sku) }}"
                @input="update($event.target)"
                x-init="update($el)"
                style="padding-right: 4rem;"
            >


            <div
                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                :class="color">

                <span x-text="count"></span>/<span x-text="max"></span>

            </div>

        </div>

    </x-char-counter>


    <div class="flex justify-between">

        <div class="text-xs text-red-500 italic mt-1">
            * Required and must be unique
        </div>

        <div class="text-xs text-gray-500 mt-1">
            Used for inventory tracking and integrations
        </div>

    </div>

</div>



</div>
<div class="flex justify-between">

<a href="{{ route('supplier.products.index') }}"
   class="mt-4 bg-gray-50 border border-gray-400 hover:bg-gray-100 text-gray-400 px-6 py-2 rounded">
    Cancel
</a>

<button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded end">
    Next
</button>

</div>

</form>