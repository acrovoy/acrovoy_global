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
                        Categories & Attributes
                    </div>

                    <div class="text-gray-200 text-sm">
                        Выберите категорию, к которой относится товар.
                        После выбора категории система автоматически загрузит атрибуты,
                        настроенные для этой категории.
                    </div>

                    <ul class="text-gray-300 text-xs space-y-2">

                        <li>
                            <span class="text-white font-medium">📂 Category</span>
                            — выберите наиболее подходящую категорию товара.
                            Категория определяет, какие характеристики будут доступны при заполнении товара.
                        </li>

                        <li>
                            <span class="text-white font-medium">⚙️ Attributes</span>
                            — характеристики товара, связанные с выбранной категорией.
                            Они могут включать материал, цвет, размеры, вес и другие параметры.
                        </li>

                        <li>
                            <span class="text-white font-medium">⭐ Required</span>
                            — обязательные характеристики необходимо заполнить,
                            чтобы продолжить публикацию товара.
                        </li>

                        <li>
                            <span class="text-white font-medium">📏 Measurements</span>
                            — параметры с числовым значением и единицей измерения,
                            например ширина, высота, глубина или вес.
                        </li>

                    </ul>

                    <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                        Совет:
                        <span class="text-gray-200">
                            Сначала выберите максимально точную категорию.
                            Это позволит системе показать только релевантные характеристики
                            и упростит заполнение карточки товара.
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
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('product/product_edit.category_attributes.title') }}</h3>

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
    window.productEditTranslations = {
        categoryAttributes: {
            title: @json(__('product/product_edit.category_attributes.title')),

            empty: {
                title: @json(__('product/product_edit.category_attributes.empty.title')),
                description: @json(__('product/product_edit.category_attributes.empty.description')),
            },

            otherAttributes: {
                title: @json(__('product/product_edit.category_attributes.other_attributes.title')),
                description: @json(__('product/product_edit.category_attributes.other_attributes.description')),
            },

            measurements: {
                title: @json(__('product/product_edit.category_attributes.measurements.title')),
                description: @json(__('product/product_edit.category_attributes.measurements.description')),
            },

            attributeGroupFallback: @json(
                __('product/product_edit.category_attributes.attribute_group_fallback')
            ),
        },

        attributes: {
            selectPlaceholder: @json(
                __('product/product_edit.attributes.select_placeholder')
            ),

            requiredError: @json(
                __('product/product_edit.attributes.required.error')
            ),

            yes: @json(
                __('product/product_edit.attributes.boolean.yes')
            ),
        }
    };
</script>

<script>
function categorySelector({ initialCategory = null, initialProductId = null } = {}) {

    const translations = window.productEditTranslations;

    return {
        levels: [],
        selectedCategory: null,
        breadcrumb: [],
        initialCategory,
        initialProductId,

        async init() {
            const res = await fetch('/dashboard/category-selector/root');
            const data = await res.json();

            this.levels = [
                {
                    items: data,
                    selected: null
                }
            ];

            if (this.initialCategory) {
                await this.loadPath(this.initialCategory);

                const submitButton = document.getElementById('step-2-submit');

                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        },


        // ================================================================
        // SELECT CATEGORY
        // ================================================================

        async selectCategory(categoryId, levelIndex) {

            const level = this.levels[levelIndex];

            level.selected = categoryId;

            // ------------------------------------------------------------
            // Breadcrumb
            // ------------------------------------------------------------

            const selectedItem = level.items.find(
                i => i.id == categoryId
            );

            if (selectedItem) {
                this.breadcrumb[levelIndex] = selectedItem.name;
            }

            // ------------------------------------------------------------
            // Remove levels below current
            // ------------------------------------------------------------

            this.levels = this.levels.slice(
                0,
                levelIndex + 1
            );

            this.breadcrumb = this.breadcrumb.slice(
                0,
                levelIndex + 1
            );

            // ------------------------------------------------------------
            // Load children
            // ------------------------------------------------------------

            const res = await fetch(
                `/dashboard/category-selector/children/${categoryId}`
            );

            const children = await res.json();

            if (children.length > 0) {

                this.levels.push({
                    items: children,
                    selected: null
                });
            }

            // ------------------------------------------------------------
            // Save selected category
            // ------------------------------------------------------------

            this.selectedCategory = categoryId;

            // ------------------------------------------------------------
            // Load attributes
            // ------------------------------------------------------------

            await this.loadAttributes(categoryId);

            const submitButton =
                document.getElementById('step-2-submit');

            if (submitButton) {
                submitButton.disabled = false;
            }
        },


        // ================================================================
        // LOAD CATEGORY PATH
        // ================================================================

        async loadPath(categoryId) {

            const res = await fetch(
                `/dashboard/category-selector/path/${categoryId}`
            );

            const path = await res.json();

            // ------------------------------------------------------------
            // Build category levels
            // ------------------------------------------------------------

            for (let i = 0; i < path.length; i++) {

                const node = path[i];

                if (!this.levels[i]) {

                    this.levels.push({
                        items: [],
                        selected: null
                    });
                }

                // --------------------------------------------------------
                // Load children
                // --------------------------------------------------------

                const resChildren = await fetch(
                    `/dashboard/category-selector/children/${node.id}`
                );

                const children = await resChildren.json();

                this.levels[i].selected = node.id;

                if (
                    children.length > 0 &&
                    this.levels[i + 1] === undefined
                ) {

                    this.levels[i + 1] = {
                        items: children,
                        selected: null
                    };
                }

                // --------------------------------------------------------
                // Breadcrumb
                // --------------------------------------------------------

                this.breadcrumb[i] = node.name;
            }

            // ------------------------------------------------------------
            // Final category
            // ------------------------------------------------------------

            this.selectedCategory = categoryId;

            await this.loadAttributes(categoryId);
        },


        // ================================================================
        // LOAD ATTRIBUTES
        // ================================================================

        async loadAttributes(categoryId) {

            console.log(
                'Load attributes for category:',
                categoryId
            );

            const query = this.initialProductId
                ? `?product_id=${this.initialProductId}`
                : '';

            const res = await fetch(
                `/dashboard/category-selector/attributes/${categoryId}${query}`
            );

            const attributes = await res.json();

            console.log(
                'ATTRIBUTES:',
                attributes
            );

            const container =
                document.getElementById('category-attributes');

            const emptyBlock =
                document.getElementById('category-attributes-empty');

            if (!container) {
                return;
            }

            // ------------------------------------------------------------
            // Clear previous attributes
            // ------------------------------------------------------------

            container.innerHTML = '';

            // ------------------------------------------------------------
            // Empty state
            // ------------------------------------------------------------

            if (!attributes.length) {

                if (emptyBlock) {
                    emptyBlock.classList.remove('hidden');
                }

                return;
            }

            if (emptyBlock) {
                emptyBlock.classList.add('hidden');
            }


            // ============================================================
            // GROUP ATTRIBUTES
            // ============================================================

            const groupedAttributes = {};

            const ungroupedAttributes = [];

            const measurementAttributes = [];


            attributes.forEach(attr => {

                // --------------------------------------------------------
                // Measurements are always separate
                // --------------------------------------------------------

                if (attr.type === 'measurement') {

                    measurementAttributes.push(attr);

                    return;
                }


                // --------------------------------------------------------
                // Resolve group
                // --------------------------------------------------------

                const groupId =
                    attr.group_id ??
                    attr.group?.id ??
                    null;


                // --------------------------------------------------------
                // No group
                // --------------------------------------------------------

                if (!groupId) {

                    ungroupedAttributes.push(attr);

                    return;
                }


                // --------------------------------------------------------
                // Create group
                // --------------------------------------------------------

                if (!groupedAttributes[groupId]) {

                    groupedAttributes[groupId] = {

                        id: groupId,

                        name:
    attr.group?.name ??
    attr.group_name ??
    translations.categoryAttributes.attributeGroupFallback,

                        sort_order:
                            attr.group?.sort_order ??
                            0,

                        attributes: []
                    };
                }


                // --------------------------------------------------------
                // Add attribute
                // --------------------------------------------------------

                groupedAttributes[groupId]
                    .attributes
                    .push(attr);
            });


            // ============================================================
            // SORT GROUPS
            // ============================================================

            const groups =
                Object.values(groupedAttributes);

            groups.sort((a, b) => {

                return (
                    (a.sort_order ?? 0) -
                    (b.sort_order ?? 0)
                );
            });


            // ============================================================
            // CREATE DEFAULT FIELD
            // ============================================================

            function createDefaultField(attr) {

                const requiredAttr =
                    attr.is_required
                        ? 'required'
                        : '';

                return `
                    <input
                        type="text"
                        name="attributes[${attr.id}]"
                        class="
                            w-full
                            h-11
                            px-3.5
                            rounded-lg
                            border
                            border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            placeholder:text-gray-400
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                        value="${attr.value ?? ''}"
                        ${requiredAttr}
                    >
                `;
            }


            // ============================================================
            // CREATE NUMBER FIELD
            // ============================================================

            function createNumberField(attr) {

                const unitSymbol =
                    attr.unit?.name ?? '';

                const requiredAttr =
                    attr.is_required
                        ? 'required'
                        : '';

                return `
                    <div class="relative">

                        <input
                            type="number"
                            name="attributes[${attr.id}]"
                            class="
                                w-full
                                h-11
                                px-3.5
                                ${unitSymbol ? 'pr-16' : ''}
                                rounded-lg
                                border
                                border-gray-200
                                bg-gray-50
                                text-sm
                                text-gray-900
                                outline-none
                                transition
                                focus:bg-white
                                focus:border-gray-400
                                focus:ring-2
                                focus:ring-gray-100
                            "
                            value="${attr.value ?? ''}"
                            ${requiredAttr}
                        >

                        ${
                            unitSymbol
                                ? `
                                    <span
                                        class="
                                            absolute
                                            right-3
                                            top-1/2
                                            -translate-y-1/2
                                            text-xs
                                            font-medium
                                            text-gray-400
                                            pointer-events-none
                                        "
                                    >
                                        ${unitSymbol}
                                    </span>
                                `
                                : ''
                        }

                    </div>
                `;
            }


            // ============================================================
            // CREATE MEASUREMENT FIELD
            // ============================================================

            function createMeasurementField(attr) {

                const requiredAttr =
                    attr.is_required
                        ? 'required'
                        : '';

                const unitsHtml =
                    (attr.units ?? [])
                        .map(unit => `
                            <option
                                value="${unit.id}"
                                ${
                                    String(attr.selected_unit_id) ===
                                    String(unit.id)
                                        ? 'selected'
                                        : ''
                                }
                            >
                                ${unit.name}
                                ${
                                    unit.symbol
                                        ? ` (${unit.symbol})`
                                        : ''
                                }
                            </option>
                        `)
                        .join('');


                const div =
                    document.createElement('div');

                div.className = `
                    group
                    flex
                    flex-col
                    min-w-0
                `;


                div.innerHTML = `
                    <div
                        class="
                            flex
                            items-center
                            min-h-[24px]
                            mb-1.5
                        "
                    >

                        <label
                            class="
                                text-[13px]
                                font-semibold
                                tracking-[-0.01em]
                                text-gray-800
                            "
                        >
                            ${attr.name}

                            ${
                                attr.is_required
                                    ? `
                                        <span
                                            class="
                                                text-red-500
                                                ml-1
                                            "
                                        >
                                            *
                                        </span>
                                    `
                                    : ''
                            }

                        </label>

                    </div>


                    <div class="w-full">

                        <div class="flex gap-2">

                            <input
                                type="number"
                                step="any"
                                name="attributes[${attr.id}][value]"
                                class="
                                    flex-1
                                    min-w-0
                                    h-11
                                    px-3.5
                                    rounded-lg
                                    border
                                    border-gray-200
                                    bg-gray-50
                                    text-sm
                                    text-gray-900
                                    outline-none
                                    transition
                                    focus:bg-white
                                    focus:border-gray-400
                                    focus:ring-2
                                    focus:ring-gray-100
                                "
                                value="${attr.value ?? ''}"
                                ${requiredAttr}
                            >

                            ${
                                attr.units?.length
                                    ? `
                                        <select
                                            name="attributes[${attr.id}][unit_id]"
                                            class="
                                                w-36
                                                h-11
                                                px-3
                                                rounded-lg
                                                border
                                                border-gray-200
                                                bg-gray-50
                                                text-sm
                                                text-gray-700
                                                outline-none
                                                transition
                                                focus:bg-white
                                                focus:border-gray-400
                                                focus:ring-2
                                                focus:ring-gray-100
                                            "
                                        >
                                            ${unitsHtml}
                                        </select>
                                    `
                                    : ''
                            }

                        </div>

                    </div>


                    ${
                        attr.is_required
                            ? `
                                <div
                                    data-required-error="${attr.id}"
                                    class="
                                        hidden
                                        mt-1.5
                                        text-[11px]
                                        text-red-500
                                    "
                                >
                                    This field is required for this category
                                </div>
                            `
                            : ''
                    }
                `;

                return div;
            }


            // ============================================================
            // CREATE SELECT FIELD
            // ============================================================

            function createSelectField(attr) {

                const requiredAttr =
                    attr.is_required
                        ? 'required'
                        : '';

                const optionsHtml =
                    [...(attr.options ?? [])]
                        .sort((a, b) => {

                            return (
                                (a.sort_order ?? 0) -
                                (b.sort_order ?? 0)
                            );
                        })
                        .map(o => `
                            <option
                                value="${o.value}"
                                ${
                                    String(attr.value) ===
                                    String(o.value)
                                        ? 'selected'
                                        : ''
                                }
                            >
                                ${o.label}
                            </option>
                        `)
                        .join('');


                return `
                    <select
                        name="attributes[${attr.id}]"
                        class="
                            w-full
                            h-11
                            px-3.5
                            rounded-lg
                            border
                            border-gray-200
                            bg-gray-50
                            text-sm
                            text-gray-900
                            outline-none
                            transition
                            focus:bg-white
                            focus:border-gray-400
                            focus:ring-2
                            focus:ring-gray-100
                        "
                        ${requiredAttr}
                    >

                        <option value="">
    ${translations.attributes.selectPlaceholder}
</option>

                        ${optionsHtml}

                    </select>
                `;
            }


            // ============================================================
            // CREATE MULTISELECT FIELD
            // ============================================================

            function createMultiselectField(attr) {

                const selectedValues =
                    Array.isArray(attr.value)
                        ? attr.value.map(String)
                        : [];


                const optionsHtml =
                    (attr.options ?? [])
                        .map(o => `
                            <label
                                class="
                                    flex
                                    items-center
                                    gap-3
                                    px-3
                                    py-2.5
                                    rounded-md
                                    cursor-pointer
                                    transition
                                    hover:bg-white
                                "
                            >

                                <input
                                    type="checkbox"
                                    name="attributes[${attr.id}][]"
                                    value="${o.value}"
                                    class="
                                        w-4
                                        h-4
                                        rounded
                                        border-gray-300
                                        text-gray-900
                                        focus:ring-gray-400
                                    "
                                    ${
                                        selectedValues.includes(
                                            String(o.value)
                                        )
                                            ? 'checked'
                                            : ''
                                    }
                                >

                                <span class="text-sm text-gray-700">
                                    ${o.label}
                                </span>

                            </label>
                        `)
                        .join('');


                return `
                    <div
                        class="
                            rounded-lg
                            border
                            border-gray-200
                            bg-gray-50
                            max-h-60
                            overflow-y-auto
                        "
                    >

                        <div class="p-2 pb-3">

                            ${optionsHtml}

                        </div>

                    </div>
                `;
            }


            // ============================================================
            // CREATE BOOLEAN FIELD
            // ============================================================

            function createBooleanField(attr) {

                const checked =
                    attr.value
                        ? 'checked'
                        : '';


                return `
                    <label
                        class="
                            inline-flex
                            items-center
                            gap-3
                            min-h-11
                            px-3.5
                            rounded-lg
                            border
                            border-gray-200
                            bg-gray-50
                            cursor-pointer
                            transition
                            hover:bg-white
                        "
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
                            class="
                                w-4
                                h-4
                                rounded
                                border-gray-300
                                text-gray-900
                                focus:ring-gray-400
                            "
                            ${checked}
                        >

                        <span
                            class="
                                text-sm
                                font-medium
                                text-gray-700
                            "
                        >
                            ${translations.attributes.yes}
                        </span>

                    </label>
                `;
            }


            // ============================================================
            // CREATE ATTRIBUTE FIELD
            // ============================================================

            function createAttributeField(attr) {

                const requiredStar =
                    attr.is_required
                        ? `
                            <span
                                class="
                                    text-red-500
                                    ml-1
                                "
                            >
                                *
                            </span>
                        `
                        : '';


                const unitSymbol =
                    attr.unit?.name ?? '';


                const unitBadge =
                    unitSymbol
                        ? `
                            <span
                                class="
                                    ml-2
                                    text-[11px]
                                    font-medium
                                    text-gray-400
                                "
                            >
                                ${unitSymbol}
                            </span>
                        `
                        : '';


                let fieldHtml;


                // --------------------------------------------------------
                // TEXT
                // --------------------------------------------------------

                if (
                    attr.type === 'text' ||
                    attr.type === 'string' ||
                    !attr.type
                ) {

                    fieldHtml =
                        createDefaultField(attr);
                }


                // --------------------------------------------------------
                // NUMBER
                // --------------------------------------------------------

                else if (
                    attr.type === 'number'
                ) {

                    fieldHtml =
                        createNumberField(attr);
                }


                // --------------------------------------------------------
                // SELECT
                // --------------------------------------------------------

                else if (
                    attr.type === 'select' &&
                    attr.options
                ) {

                    fieldHtml =
                        createSelectField(attr);
                }


                // --------------------------------------------------------
                // MULTISELECT
                // --------------------------------------------------------

                else if (
                    attr.type === 'multiselect' &&
                    attr.options
                ) {

                    fieldHtml =
                        createMultiselectField(attr);
                }


                // --------------------------------------------------------
                // BOOLEAN
                // --------------------------------------------------------

                else if (
                    attr.type === 'boolean'
                ) {

                    fieldHtml =
                        createBooleanField(attr);
                }


                // --------------------------------------------------------
                // FALLBACK
                // --------------------------------------------------------

                else {

                    fieldHtml =
                        createDefaultField(attr);
                }


                // --------------------------------------------------------
                // Attribute wrapper
                // --------------------------------------------------------

                const div =
                    document.createElement('div');

                div.className = `
                    group
                    flex
                    flex-col
                    min-w-0
                `;


                div.innerHTML = `
                    <div
                        class="
                            flex
                            items-center
                            min-h-[24px]
                            mb-1.5
                        "
                    >

                        <label
                            class="
                                text-[13px]
                                font-semibold
                                tracking-[-0.01em]
                                text-gray-800
                            "
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
                                    class="
                                        hidden
                                        mt-1.5
                                        text-[11px]
                                        text-red-500
                                    "
                                >
                                    ${translations.attributes.requiredError}
                                </div>
                            `
                            : ''
                    }
                `;


                return div;
            }


            // ============================================================
            // RENDER ATTRIBUTE GROUP
            // ============================================================

            function renderGroup(group, isFirst = false) {

                group.attributes.sort((a, b) => {

                    return (
                        (a.sort_order ?? 0) -
                        (b.sort_order ?? 0)
                    );
                });


                const groupBlock =
                    document.createElement('div');


                groupBlock.className = `
                    md:col-span-2
                    ${isFirst ? '' : 'pt-6 border-t border-gray-200'}
                `;


                groupBlock.innerHTML = `
                    <div class="mb-4">

                        <h3
                            class="
                                text-base
                                font-semibold
                                tracking-[-0.01em]
                                text-gray-900
                            "
                        >
                            ${group.name}
                        </h3>

                    </div>


                    <div
                        class="
                            grid
                            grid-cols-1
                            md:grid-cols-2
                            gap-x-6
                            gap-y-5
                            rounded-xl
                            border
                            border-gray-200
                            bg-gray-50/50
                            p-5
                        "
                    ></div>
                `;


                const fieldsContainer =
                    groupBlock.querySelector(
                        'div.grid'
                    );


                group.attributes.forEach(attr => {

                    fieldsContainer.appendChild(
                        createAttributeField(attr)
                    );
                });


                container.appendChild(
                    groupBlock
                );
            }


            // ============================================================
            // RENDER GROUPS
            // ============================================================

            groups.forEach((group, index) => {

                renderGroup(
                    group,
                    index === 0
                );
            });


            // ============================================================
            // UNGROUPED ATTRIBUTES
            // ============================================================

            if (ungroupedAttributes.length) {

                ungroupedAttributes.sort((a, b) => {

                    return (
                        (a.sort_order ?? 0) -
                        (b.sort_order ?? 0)
                    );
                });


                const ungroupedBlock =
                    document.createElement('div');


                ungroupedBlock.className = `
                    md:col-span-2
                    pt-6
                    border-t
                    border-gray-200
                `;


                ungroupedBlock.innerHTML = `
                    <div class="mb-4">

                        <h3
                            class="
                                text-base
                                font-semibold
                                tracking-[-0.01em]
                                text-gray-900
                            "
                        >
                            ${translations.categoryAttributes.otherAttributes.title}
                        </h3>

                        <p
                            class="
                                mt-1
                                text-xs
                                text-gray-500
                            "
                        >
                            ${translations.categoryAttributes.otherAttributes.description}
                        </p>

                    </div>


                    <div
                        class="
                            grid
                            grid-cols-1
                            md:grid-cols-2
                            gap-x-6
                            gap-y-5
                            rounded-xl
                            border
                            border-gray-200
                            bg-gray-50/50
                            p-5
                        "
                    ></div>
                `;


                const fieldsContainer =
                    ungroupedBlock.querySelector(
                        'div.grid'
                    );


                ungroupedAttributes.forEach(attr => {

                    fieldsContainer.appendChild(
                        createAttributeField(attr)
                    );
                });


                container.appendChild(
                    ungroupedBlock
                );
            }


            // ============================================================
            // MEASUREMENTS
            // ============================================================

            if (measurementAttributes.length) {

                measurementAttributes.sort((a, b) => {

                    return (
                        (a.sort_order ?? 0) -
                        (b.sort_order ?? 0)
                    );
                });


                const measurementBlock =
                    document.createElement('div');


                measurementBlock.className = `
                    md:col-span-2
                    pt-6
                    border-t
                    border-gray-200
                `;


                measurementBlock.innerHTML = `
                    <div class="mb-4">

                        <h3
                            class="
                                text-base
                                font-semibold
                                tracking-[-0.01em]
                                text-gray-900
                            "
                        >
                            ${translations.categoryAttributes.measurements.title}
                        </h3>

                        <p
                            class="
                                mt-1
                                text-xs
                                text-gray-500
                            "
                        >
                            ${translations.categoryAttributes.measurements.description}
                        </p>

                    </div>


                    <div
                        class="
                            grid
                            grid-cols-1
                            md:grid-cols-2
                            gap-x-6
                            gap-y-5
                            rounded-xl
                            border
                            border-gray-200
                            bg-gray-50/50
                            p-5
                        "
                    ></div>
                `;


                const measurementContainer =
                    measurementBlock.querySelector(
                        'div.grid'
                    );


                measurementAttributes.forEach(attr => {

                    measurementContainer.appendChild(
                        createMeasurementField(attr)
                    );
                });


                container.appendChild(
                    measurementBlock
                );
            }
        }
    }
}
</script>