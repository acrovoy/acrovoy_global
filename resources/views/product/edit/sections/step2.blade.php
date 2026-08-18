

@include('product.edit.partials.progress-bar', [$mode = 'edit'])

<form method="POST"
            action="{{ route('supplier.products.update', [
          'product' => $product->id,
          'step' => 2
      ]) }}"
            enctype="multipart/form-data"
            class="" id="productForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="user_id" value="{{ auth()->id() }}">


<div x-data="categorySelector({ initialCategory: {{ $product->category_id ?? 'null' }}, initialProductId: {{ $product->id ?? 'null' }} })" x-init="init()">

    <h3 class="text-xl font-semibold mb-4">Category

   <x-help-tooltip width="w-96">
        <div class="space-y-3 leading-relaxed">
            <div class="font-semibold text-white">
                RFQ Visibility
            </div>
            <div class="text-gray-200 text-sm">
                Выберите, кто сможет увидеть ваш RFQ и отправить на него предложение.
                Чем шире видимость, тем больше потенциальных поставщиков сможет откликнуться.
            </div>
            <ul class="text-gray-300 text-xs space-y-2">
                <li>
                    <span class="text-white font-medium">🔒 Private</span>
                    — RFQ увидят только поставщики, которых вы пригласите.
                    Подходит для работы с конкретными или проверенными поставщиками.
                </li>
                <li>
                    <span class="text-white font-medium">🧭 Category</span>
                    — RFQ будет доступен поставщикам, работающим в выбранной категории.
                    Это поможет получить предложения от подходящих специалистов.
                </li>
                <li>
                    <span class="text-white font-medium">🌐 Platform</span>
                    — RFQ смогут увидеть все зарегистрированные поставщики платформы.
                    Подходит, если вы хотите получить больше предложений и сравнить поставщиков.
                </li>
                <li>
                    <span class="text-white font-medium">🚀 Open</span>
                    — RFQ станет публичным и сможет отображаться в открытом разделе RFQ.
                    Его смогут увидеть даже незарегистрированные посетители сайта.
                    Для отправки предложения поставщику потребуется зарегистрироваться.
                </li>
            </ul>
            <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                Совет:
                <span class="text-gray-200">
                    Используйте Private для конкретных поставщиков,
                    Category для поиска профильных поставщиков,
                    Platform для максимального охвата внутри Acrovoy,
                    а Open — если хотите привлечь новых поставщиков через публичный сайт.
                </span>
            </div>

        </div>
    </x-help-tooltip>

    </h3>

    <!-- Levels -->
    <template x-for="(level, index) in levels" :key="index">
        <div class="mb-3">
            <select class="input w-full" @change="selectCategory($event.target.value, index)">
                <option value="">Select category</option>
                <template x-for="item in level.items" :key="item.id">
                    <option :value="item.id" x-text="item.name" :selected="item.id == level.selected"></option>
                </template>
            </select>
        </div>
    </template>

    <input type="hidden" name="category" x-model="selectedCategory">

    <div x-show="breadcrumb.length" class="text-sm text-gray-600 mt-3">
        Selected category: <span class="font-medium" x-text="breadcrumb.join(' → ')"></span>
    </div>

    <!-- Attributes Block -->
    <div x-show="selectedCategory !== null" x-transition class="mt-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Attributes</h3>

            <div id="category-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"></div>

            <div id="category-attributes-empty" class="hidden text-sm text-gray-400 italic">
                No specifications available for this category
            </div>
        </div>
    </div>
</div>


</div>
<div class="flex justify-between">

<a href="{{ route('supplier.products.edit-step', [$product->id, 1]) }}"
   class="mt-4 bg-gray-50 border border-gray-400 hover:bg-gray-100 text-gray-400 px-6 py-2 rounded">
    Previous
</a>



<button disabled id="step-2-submit" type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded">
    Next
</button>

</div>

</form>


<script>
function categorySelector({ initialCategory = null, initialProductId = null } = {}) {
    return {
        levels: [],
        selectedCategory: null,
        breadcrumb: [],
        initialCategory,
        initialProductId,

        async init() {
            const res = await fetch('/dashboard/category-selector/root');
            const data = await res.json();
            this.levels = [{ items: data, selected: null }];

            if (this.initialCategory) {
                await this.loadPath(this.initialCategory);
                
                document.getElementById('step-2-submit').disabled = false;    
            }
        },

        async selectCategory(categoryId, levelIndex) {
            const level = this.levels[levelIndex];
            level.selected = categoryId;

            // Обновляем breadcrumb
            const selectedItem = level.items.find(i => i.id == categoryId);
            if (selectedItem) this.breadcrumb[levelIndex] = selectedItem.name;

            // Убираем уровни ниже текущего
            this.levels = this.levels.slice(0, levelIndex + 1);
            this.breadcrumb = this.breadcrumb.slice(0, levelIndex + 1);

            // Подгружаем дочерние категории
            const res = await fetch(`/dashboard/category-selector/children/${categoryId}`);
            const children = await res.json();            

            if (children.length > 0) {
                this.levels.push({ items: children, selected: null });
            }

            // Всегда сохраняем выбранную категорию и подгружаем атрибуты
            this.selectedCategory = categoryId;
            await this.loadAttributes(categoryId);

            document.getElementById('step-2-submit').disabled = false;
        },

        async loadPath(categoryId) {
            const res = await fetch(`/dashboard/category-selector/path/${categoryId}`);
            const path = await res.json();

            // Проходим по каждому уровню пути и создаем select
            for (let i = 0; i < path.length; i++) {
                const node = path[i];
                // Если уровень уже существует, используем его
                if (!this.levels[i]) {
                    this.levels.push({ items: [], selected: null });
                }

                // Загружаем детей для уровня
                const resChildren = await fetch(`/dashboard/category-selector/children/${node.id}`);
                const children = await resChildren.json();
    

                // this.levels[i].items = children; // текущий node
                this.levels[i].selected = node.id;

                if (children.length > 0 && this.levels[i + 1] === undefined) {
                    this.levels[i + 1] = { items: children, selected: null };
                }

                // Обновляем breadcrumb
                this.breadcrumb[i] = node.name;
            }

            // Подгружаем атрибуты для конечной категории
            this.selectedCategory = categoryId;
            await this.loadAttributes(categoryId);
        },

        async loadAttributes(categoryId) {
            console.log('Load attributes for category:', categoryId);
            const query = this.initialProductId ? `?product_id=${this.initialProductId}` : '';
            const res = await fetch(`/dashboard/category-selector/attributes/${categoryId}${query}`);
            const attributes = await res.json();
            console.log('ATTRIBUTES:', attributes);

            const container = document.getElementById('category-attributes');
            const emptyBlock = document.getElementById('category-attributes-empty');

            container.innerHTML = '';
            if (!attributes.length) {
                emptyBlock.classList.remove('hidden');
                return;
            }
            emptyBlock.classList.add('hidden');







            // ==========================================================================
// SORT ATTRIBUTES BY TYPE
// ==========================================================================

const typeOrder = {
    select: 1,
    multiselect: 2,
    number: 3,
    text: 4,
    boolean: 5,
};

attributes.sort((a, b) => {
    return (typeOrder[a.type] ?? 99) - (typeOrder[b.type] ?? 99);
});


// ==========================================================================
// FORM ATTRIBUTES
// ==========================================================================

attributes.forEach(attr => {

    const requiredStar = attr.is_required
        ? `<span class="text-red-500 ml-1">*</span>`
        : '';

    const requiredAttr = attr.is_required
        ? 'required'
        : '';

    /*
    |--------------------------------------------------------------------------
    | UNIT
    |--------------------------------------------------------------------------
    |
    | attr.unit comes from Unit relation:
    |
    | {
    |     id: 2,
    |     code: "cm",
    |     symbol: "cm",
    |     name: "Centimeter"
    | }
    |
    */

    const unitSymbol = attr.unit?.name ?? '';

    const unitBadge = unitSymbol
        ? `
            <span class="ml-2 text-[11px] font-medium text-gray-400">
                ${unitSymbol}
            </span>
        `
        : '';


    /*
    |--------------------------------------------------------------------------
    | DEFAULT FIELD
    |--------------------------------------------------------------------------
    */

    let fieldHtml = `
        <input
            type="text"
            name="attributes[${attr.id}]"
            class="w-full h-11 px-3.5
                   rounded-lg
                   border border-gray-200
                   bg-gray-50
                   text-sm text-gray-900
                   placeholder:text-gray-400
                   outline-none
                   transition
                   focus:bg-white
                   focus:border-gray-400
                   focus:ring-2
                   focus:ring-gray-100"
            value="${attr.value ?? ''}"
            ${requiredAttr}
        >
    `;


    /*
    |--------------------------------------------------------------------------
    | NUMBER
    |--------------------------------------------------------------------------
    */

    if (attr.type === 'number') {

        fieldHtml = `
            <div class="relative">

                <input
                    type="number"
                    name="attributes[${attr.id}]"
                    class="w-full h-11 px-3.5
                           ${unitSymbol ? 'pr-16' : ''}
                           rounded-lg
                           border border-gray-200
                           bg-gray-50
                           text-sm text-gray-900
                           outline-none
                           transition
                           focus:bg-white
                           focus:border-gray-400
                           focus:ring-2
                           focus:ring-gray-100"
                    value="${attr.value ?? ''}"
                    ${requiredAttr}
                >

                ${
                    unitSymbol
                        ? `
                            <span
                                class="absolute right-3 top-1/2
                                       -translate-y-1/2
                                       text-xs font-medium
                                       text-gray-400
                                       pointer-events-none"
                            >
                                ${unitSymbol}
                            </span>
                        `
                        : ''
                }

            </div>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | SELECT
    |--------------------------------------------------------------------------
    */

    else if (attr.type === 'select' && attr.options) {

        const optionsHtml = [...attr.options]
            .sort(
                (a, b) =>
                    (a.sort_order ?? 0) -
                    (b.sort_order ?? 0)
            )
            .map(o => `
                <option
                    value="${o.value}"
                    ${attr.value == o.value ? 'selected' : ''}
                >
                    ${o.label}
                </option>
            `)
            .join('');

        fieldHtml = `
            <select
                name="attributes[${attr.id}]"
                class="w-full h-11 px-3.5
                       rounded-lg
                       border border-gray-200
                       bg-gray-50
                       text-sm text-gray-900
                       outline-none
                       transition
                       focus:bg-white
                       focus:border-gray-400
                       focus:ring-2
                       focus:ring-gray-100"
                ${requiredAttr}
            >
                <option value="">Select...</option>

                ${optionsHtml}

            </select>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | MULTISELECT
    |--------------------------------------------------------------------------
    */

    else if (attr.type === 'multiselect' && attr.options) {

        const selectedValues = Array.isArray(attr.value)
            ? attr.value.map(String)
            : [];

        const optionsHtml = attr.options
            .map(o => `
                <label
                    class="flex items-center gap-3
                           px-3 py-2.5
                           rounded-md
                           cursor-pointer
                           transition
                           hover:bg-white"
                >

                    <input
                        type="checkbox"
                        name="attributes[${attr.id}][]"
                        value="${o.value}"
                        class="w-4 h-4
                               rounded
                               border-gray-300
                               text-gray-900
                               focus:ring-gray-400"
                        ${selectedValues.includes(String(o.value)) ? 'checked' : ''}
                    >

                    <span class="text-sm text-gray-700">
                        ${o.label}
                    </span>

                </label>
            `)
            .join('');

        fieldHtml = `
            <div
                class="rounded-lg
                       border border-gray-200
                       bg-gray-50
                       p-1.5
                       max-h-60
                       overflow-y-auto"
            >

                ${optionsHtml}

            </div>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | BOOLEAN
    |--------------------------------------------------------------------------
    */

    else if (attr.type === 'boolean') {

        const checked = attr.value
            ? 'checked'
            : '';

        fieldHtml = `
            <label
                class="inline-flex items-center gap-3
                       min-h-11
                       px-3.5
                       rounded-lg
                       border border-gray-200
                       bg-gray-50
                       cursor-pointer
                       transition
                       hover:bg-white"
            >

                <input
                    type="hidden"
                    name="attributes[${attr.id}]"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="attributes[${attr.id}]"
                    value="1"
                    class="w-4 h-4
                           rounded
                           border-gray-300
                           text-gray-900
                           focus:ring-gray-400"
                    ${checked}
                >

                <span class="text-sm font-medium text-gray-700">
                    Yes
                </span>

            </label>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE ROW
    |--------------------------------------------------------------------------
    */

    const div = document.createElement('div');

    div.className = `
        group
        flex flex-col
        min-w-0
    `;

    div.innerHTML = `

        <div class="flex items-center min-h-[24px] mb-1.5">

            <label
                class="text-[13px]
                       font-semibold
                       tracking-[-0.01em]
                       text-gray-800"
            >
                ${attr.name}

                ${requiredStar}

            </label>

            ${unitBadge}

        </div>


        <div class="w-full">
            ${fieldHtml}
        </div>


        ${
            attr.is_required
                ? `
                    <div
                        data-required-error="${attr.id}"
                        class="hidden mt-1.5 text-[11px] text-red-500"
                    >
                        This field is required for this category
                    </div>
                `
                : ''
        }

    `;

    container.appendChild(div);
});







        }
    }
}
</script>