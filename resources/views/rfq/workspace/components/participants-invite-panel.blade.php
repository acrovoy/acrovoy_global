
<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-4">

    <div class="text-sm font-semibold flex items-center gap-2">
        Invite Suppliers

        <x-help-tooltip width="w-96">
        <div class="space-y-3 leading-relaxed">

            <div class="font-semibold text-white">
                Inviting Suppliers
            </div>

            <div class="text-gray-200 text-sm">
                Вы можете пригласить поставщика двумя способами:
                выбрать его из списка зарегистрированных участников
                или отправить приглашение по email.
            </div>

            <ul class="text-gray-300 text-xs space-y-2">

                <li>
                    <span class="text-white font-medium">
                        👤 Select from list
                    </span>
                    — выберите поставщика из списка зарегистрированных
                    участников платформы.
                </li>

                <li>
                    <span class="text-white font-medium">
                        ✉️ Invite by Email
                    </span>
                    — введите email поставщика.
                    Если у него уже есть аккаунт, приглашение будет связано
                    с его профилем. Если аккаунта нет, он сможет зарегистрироваться
                    и присоединиться к RFQ по приглашению.
                </li>

            </ul>

            <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                Совет:
                <span class="text-gray-200">
                    Используйте выбор из списка для поставщиков,
                    которых вы уже знаете на платформе.
                    Email-приглашение удобно для новых поставщиков,
                    которых ещё нет в Acrovoy.
                </span>
            </div>

        </div>
    </x-help-tooltip>


    </div>

    {{-- ========================= --}}
    {{-- PRIVATE MODE --}}
    {{-- ========================= --}}
    @if($visibility === 'private')

        <div class="text-xs text-gray-500">
            Invite specific suppliers from the platform or by email.
        </div>

        {{-- INVITE FROM PLATFORM --}}
        <form method="POST"
      action="{{ route('buyer.rfq.participants.store', $rfq) }}"
      class="flex gap-2">

    @csrf

    <div
    x-data="supplierSelector()"
    class="relative w-full"
>

    {{-- Hidden fields --}}
    <input type="hidden"
           name="participant_id"
           x-model="selectedId">

    <input type="hidden"
           name="participant_type"
           value="{{ \App\Models\Supplier::class }}">

    {{-- SELECTOR --}}
    <button
        type="button"
        @click="open = !open"
        class="w-full flex items-center justify-between gap-3
               border border-gray-300 rounded-lg
               bg-white px-3 py-2.5
               text-left
               hover:border-gray-400
               focus:outline-none
               focus:ring-2 focus:ring-gray-200"
    >

        <template x-if="selected">
            <div class="flex items-center gap-3 min-w-0">

                {{-- LOGO --}}
                <div class="w-9 h-9 shrink-0 rounded-lg overflow-hidden
                            border border-gray-200 bg-gray-50
                            flex items-center justify-center">

                    <template x-if="selected.logo">
                        <img
                            :src="selected.logo"
                            class="w-full h-full object-cover"
                        >
                    </template>

                    <template x-if="!selected.logo">
                        <span
                            class="text-xs font-semibold text-gray-500"
                            x-text="selected.initial"
                        ></span>
                    </template>

                </div>

                {{-- COMPANY --}}
                <div class="min-w-0">

                    <div
                        class="text-sm font-medium text-gray-900 truncate"
                        x-text="selected.name"
                    ></div>

                    <div class="text-[11px] text-gray-400">
                        Supplier
                    </div>

                </div>

            </div>
        </template>

        <template x-if="!selected">
            <span class="text-sm text-gray-400">
                Select supplier
            </span>
        </template>


        {{-- ARROW --}}
        <svg
            class="w-4 h-4 text-gray-400 shrink-0 transition"
            :class="{ 'rotate-180': open }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m6 9 6 6 6-6"
            />
        </svg>

    </button>


    {{-- DROPDOWN --}}
    <div
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="absolute z-50 mt-2 w-full
               bg-white
               border border-gray-200
               rounded-xl
               shadow-xl
               overflow-hidden"
        style="display: none;"
    >

        {{-- SEARCH --}}
        <div class="p-2 border-b border-gray-100">

            <div class="relative">

                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2
                           w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0 0 10.6 10.6Z"
                    />
                </svg>

                <input
                    type="text"
                    x-model="search"
                    @keydown.escape="open = false"
                    placeholder="Search suppliers..."
                    class="w-full
                           border border-gray-200
                           rounded-lg
                           pl-9 pr-3 py-2
                           text-sm
                           focus:outline-none
                           focus:ring-2 focus:ring-gray-200"
                >

            </div>

        </div>


        {{-- SUPPLIER LIST --}}
        <div class="max-h-64 overflow-y-auto p-1">

            <template x-for="supplier in filteredSuppliers" :key="supplier.id">

                <button
                    type="button"
                    @click="select(supplier)"
                    class="w-full flex items-center gap-3
                           px-3 py-2.5
                           rounded-lg
                           text-left
                           hover:bg-gray-50
                           transition"
                >

                    {{-- LOGO --}}
                    <div
                        class="w-9 h-9 shrink-0
                               rounded-lg
                               overflow-hidden
                               border border-gray-200
                               bg-gray-50
                               flex items-center justify-center"
                    >

                        <template x-if="supplier.logo">
                            <img
                                :src="supplier.logo"
                                class="w-full h-full object-cover"
                            >
                        </template>

                        <template x-if="!supplier.logo">
                            <span
                                class="text-xs font-semibold text-gray-500"
                                x-text="supplier.initial"
                            ></span>
                        </template>

                    </div>


                    {{-- NAME --}}
                    <div class="min-w-0 flex-1">

                        <div
                            class="text-sm font-medium text-gray-900 truncate"
                            x-text="supplier.name"
                        ></div>

                        <div class="text-[11px] text-gray-400">
                            Supplier
                        </div>

                    </div>


                    {{-- CHECK --}}
                    <template x-if="selectedId == supplier.id">

                        <svg
                            class="w-4 h-4 text-green-600 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m5 12 4 4L19 7"
                            />
                        </svg>

                    </template>

                </button>

            </template>


            {{-- EMPTY --}}
            <div
                x-show="filteredSuppliers.length === 0"
                class="px-4 py-6 text-center text-sm text-gray-400"
            >
                No suppliers found.
            </div>

        </div>

    </div>

</div>

    <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
        Invite
    </button>

</form>

{{-- ========================= --}}
{{-- CATEGORY MODE (MULTI) --}}
{{-- ========================= --}}
@elseif($visibility === 'category')

    <div class="text-xs text-gray-500 mb-2">
        Select categories whose suppliers can discover this RFQ.
        
    </div>

    <form method="POST"
          action="{{ route('buyer.rfq.visibility.category.update', $rfq) }}"
          class="space-y-3">

        @csrf
        @method('PATCH')

        <div class="border border-gray-200 rounded p-3 max-h-64 overflow-y-auto space-y-2">

            @foreach($allCategories ?? [] as $category)

                <label class="flex items-center gap-2 text-sm text-gray-700">

                    <input type="checkbox"
       name="category_ids[]"
       value="{{ $category->id }}"
       class="rounded border-gray-300"

       @if(in_array($category->id, $selectedCategoryIds ?? []))
           checked
       @endif
>

                    <span>{{ $category->name }}</span>

                </label>

            @endforeach

        </div>

        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
            Save Categories
        </button>

    </form>

        {{-- optional manual invite --}}
        <div class="pt-2 border-t border-gray-200">

            <form method="POST"
                  action="{{ route('buyer.rfq.participants.store', $rfq) }}"
                  class="flex gap-2">

                @csrf

                <div
    x-data="supplierSelector()"
    class="relative w-full"
>

    {{-- Hidden fields --}}
    <input type="hidden"
           name="participant_id"
           x-model="selectedId">

    <input type="hidden"
           name="participant_type"
           value="{{ \App\Models\Supplier::class }}">

    {{-- SELECTOR --}}
    <button
        type="button"
        @click="open = !open"
        class="w-full flex items-center justify-between gap-3
               border border-gray-300 rounded-lg
               bg-white px-3 py-2.5
               text-left
               hover:border-gray-400
               focus:outline-none
               focus:ring-2 focus:ring-gray-200"
    >

        <template x-if="selected">
            <div class="flex items-center gap-3 min-w-0">

                {{-- LOGO --}}
                <div class="w-9 h-9 shrink-0 rounded-lg overflow-hidden
                            border border-gray-200 bg-gray-50
                            flex items-center justify-center">

                    <template x-if="selected.logo">
                        <img
                            :src="selected.logo"
                            class="w-full h-full object-cover"
                        >
                    </template>

                    <template x-if="!selected.logo">
                        <span
                            class="text-xs font-semibold text-gray-500"
                            x-text="selected.initial"
                        ></span>
                    </template>

                </div>

                {{-- COMPANY --}}
                <div class="min-w-0">

                    <div
                        class="text-sm font-medium text-gray-900 truncate"
                        x-text="selected.name"
                    ></div>

                    <div class="text-[11px] text-gray-400">
                        Supplier
                    </div>

                </div>

            </div>
        </template>

        <template x-if="!selected">
            <span class="text-sm text-gray-400">
                Select supplier
            </span>
        </template>


        {{-- ARROW --}}
        <svg
            class="w-4 h-4 text-gray-400 shrink-0 transition"
            :class="{ 'rotate-180': open }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m6 9 6 6 6-6"
            />
        </svg>

    </button>


    {{-- DROPDOWN --}}
    <div
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="absolute z-50 mt-2 w-full
               bg-white
               border border-gray-200
               rounded-xl
               shadow-xl
               overflow-hidden"
        style="display: none;"
    >

        {{-- SEARCH --}}
        <div class="p-2 border-b border-gray-100">

            <div class="relative">

                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2
                           w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0 0 10.6 10.6Z"
                    />
                </svg>

                <input
                    type="text"
                    x-model="search"
                    @keydown.escape="open = false"
                    placeholder="Search suppliers..."
                    class="w-full
                           border border-gray-200
                           rounded-lg
                           pl-9 pr-3 py-2
                           text-sm
                           focus:outline-none
                           focus:ring-2 focus:ring-gray-200"
                >

            </div>

        </div>


        {{-- SUPPLIER LIST --}}
        <div class="max-h-64 overflow-y-auto p-1">

            <template x-for="supplier in filteredSuppliers" :key="supplier.id">

                <button
                    type="button"
                    @click="select(supplier)"
                    class="w-full flex items-center gap-3
                           px-3 py-2.5
                           rounded-lg
                           text-left
                           hover:bg-gray-50
                           transition"
                >

                    {{-- LOGO --}}
                    <div
                        class="w-9 h-9 shrink-0
                               rounded-lg
                               overflow-hidden
                               border border-gray-200
                               bg-gray-50
                               flex items-center justify-center"
                    >

                        <template x-if="supplier.logo">
                            <img
                                :src="supplier.logo"
                                class="w-full h-full object-cover"
                            >
                        </template>

                        <template x-if="!supplier.logo">
                            <span
                                class="text-xs font-semibold text-gray-500"
                                x-text="supplier.initial"
                            ></span>
                        </template>

                    </div>


                    {{-- NAME --}}
                    <div class="min-w-0 flex-1">

                        <div
                            class="text-sm font-medium text-gray-900 truncate"
                            x-text="supplier.name"
                        ></div>

                        <div class="text-[11px] text-gray-400">
                            Supplier
                        </div>

                    </div>


                    {{-- CHECK --}}
                    <template x-if="selectedId == supplier.id">

                        <svg
                            class="w-4 h-4 text-green-600 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m5 12 4 4L19 7"
                            />
                        </svg>

                    </template>

                </button>

            </template>


            {{-- EMPTY --}}
            <div
                x-show="filteredSuppliers.length === 0"
                class="px-4 py-6 text-center text-sm text-gray-400"
            >
                No suppliers found.
            </div>

        </div>

    </div>

</div>

                <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
                    Invite
                </button>

            </form>

        </div>

     {{-- ========================= --}}
    {{-- PLATFORM MODE (ONLY EMAIL) --}}
    {{-- ========================= --}}
    @elseif($visibility === 'platform')

        <div class="text-xs text-gray-500">
            Platform mode — invite suppliers only via email
        </div>


    {{-- ========================= --}}
    {{-- OPEN MODE (future) --}}
    {{-- ========================= --}}
    @elseif($visibility === 'open')

        <div class="text-xs text-gray-400">
            Open RFQ — public discovery mode (not active yet)
        </div>

    @endif

    {{-- ========================= --}}
    {{-- EMAIL INVITE (GLOBAL) --}}
    {{-- ========================= --}}
    <form method="POST"
          action="{{ route('buyer.rfq.participants.store', $rfq) }}"
          class="flex gap-2 pt-3 border-t border-gray-200">

        @csrf

        <input type="email"
               name="email"
               placeholder="supplier@email.com"
               class="border border-gray-300 rounded px-3 py-2 text-sm w-full">

        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
            Invite by Email
        </button>

    </form>

</div>

<script>
function supplierSelector() {

    return {

        open: false,

        search: '',

        selectedId: '',

        selected: null,

        suppliers: @js($allSuppliers),

        get filteredSuppliers() {

            const query = this.search
                .trim()
                .toLowerCase();

            if (!query) {
                return this.suppliers;
            }

            return this.suppliers.filter(supplier =>
                supplier.name
                    .toLowerCase()
                    .includes(query)
            );
        },

        select(supplier) {

            this.selectedId = supplier.id;

            this.selected = supplier;

            this.open = false;

            this.search = '';
        },

    }
}
</script>