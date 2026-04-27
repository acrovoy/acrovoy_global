<div x-data="categorySelector()" x-init="init()">

    <h3 class="text-xl font-semibold mb-4">Category & Basic Specifications

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">Category & Basic Specifications</div>
                <div class="text-gray-200 text-sm">
                    Выберите категорию соответствующую товару и система автоматически подтянет
                    стандартные характеристики (например, размеры, материал, цвет и т.д.).

                </div>
                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">

                    <li>Вы сможете добавить дополнительные спецификации в разделе "Additional Specifications"</li>
                    <li>Старайтесь не выбирать категории «Общее» . Если подходящего раздела нет, напишите на support@acrovoy.com</li>
                </ul>
                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Пример: <span class="text-gray-200">Двухместный диван для зоны ожидания» нужно добавить в «Furniture ➝ Indoor Furniture ➝ Sofas».</span>
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
                    <option :value="item.id" x-text="item.name"></option>
                </template>

            </select>
        </div>
    </template>


    <input type="hidden" name="category" x-model="selectedCategory">


    <div x-show="breadcrumb.length"
        class="text-sm text-gray-600 mt-3">

        Selected category:
        <span class="font-medium"
            x-text="breadcrumb.join(' → ')"></span>

    </div>


    <!-- CATEGORY ATTRIBUTES BLOCK -->

    <div x-show="selectedCategory"
        x-transition
        class="mt-6">

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">

            <div class="flex items-center justify-between mb-4">
<div>
                <h3 class="text-lg font-semibold text-gray-900">
                    Category Basic Specifications
                </h3>
                
</div>
                <span class="text-xs text-gray-400">
                    
                </span>

            </div>


            <div id="category-attributes"
                class="grid grid-cols-1 md:grid-cols-2 gap-4">
            </div>


            <div id="category-attributes-empty"
                class="hidden text-sm text-gray-400 italic">

                No specifications available for this category

            </div>

        </div>

    </div>


</div>



<script>
   
    function categorySelector() {
        return {
            levels: [],
            selectedCategory: null,
            breadcrumb: [],

            async init() {
                const res = await fetch('/dashboard/category-selector/root');
                const data = await res.json();
                this.levels = [{
                    items: data
                }];
            },

            async selectCategory(categoryId, levelIndex) {
                this.selectedCategory = null;
                this.breadcrumb = this.breadcrumb.slice(0, levelIndex);
                this.levels = this.levels.slice(0, levelIndex + 1);

                const level = this.levels[levelIndex];
                const selectedItem = level.items.find(i => i.id == categoryId);

                if (selectedItem) {
                    this.breadcrumb[levelIndex] = selectedItem.name;
                }

                const res = await fetch(`/dashboard/category-selector/children/${categoryId}`);
                const children = await res.json();

                if (children.length > 0) {
                    this.levels.push({
                        items: children
                    });
                } else {
                    this.selectedCategory = categoryId;
                    this.loadAttributes(categoryId);
                }
            },

            async loadAttributes(categoryId) {
                const res = await fetch(`/dashboard/category-selector/attributes/${categoryId}`);
                const attributes = await res.json();

                console.log(attributes);

                const container = document.getElementById('category-attributes');
                const emptyBlock = document.getElementById('category-attributes-empty');

                container.innerHTML = '';

                if (!attributes.length) {
                    emptyBlock.classList.remove('hidden');
                    return;
                }

                emptyBlock.classList.add('hidden');

                attributes.forEach(attr => {

    

    const requiredStar = attr.is_required
        ? `<span class="text-red-500 ml-1">*</span>`
        : '';

    const requiredAttr = attr.is_required ? 'required' : '';

    const unitBadge = attr.unit
        ? `<span class="ml-2 text-[10px] text-red-400">(${attr.unit})</span>`
        : '';

    let fieldHtml = '';

    switch(attr.type) {

        case 'text':
            fieldHtml = `
                <input type="text"
                       name="attributes[${attr.id}]"
                       class="input w-full"
                       ${requiredAttr}>
            `;
            break;

        case 'number':
            fieldHtml = `
                <input type="number"
                       name="attributes[${attr.id}]"
                       class="input w-full"
                       ${requiredAttr}>
            `;
            break;

        case 'select':
            if(attr.options) {

                const optionsHtml = attr.options
                    .map(o => `<option value="${o.value}">${o.label}</option>`)
                    .join('');

                fieldHtml = `
                    <select name="attributes[${attr.id}]"
                            class="input w-full"
                            ${requiredAttr}>
                        <option value="">Select...</option>
                        ${optionsHtml}
                    </select>
                `;
            }
            break;

        case 'multiselect':
            if(attr.options) {

                const optionsHtml = attr.options
                    .map(o => `
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox"
                                   name="attributes[${attr.id}][]"
                                   value="${o.value}"
                                   class="rounded border-gray-300">
                            ${o.label}
                        </label>
                    `).join('');

                fieldHtml = `
                    <div class="flex flex-col gap-2">
                        ${optionsHtml}
                    </div>
                `;
            }
            break;

        case 'boolean':
            fieldHtml = `
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox"
                           name="attributes[${attr.id}]"
                           value="1"
                           class="rounded border-gray-300">
                    Yes
                </label>
            `;
            break;

        default:
            fieldHtml = `
                <input type="text"
                       name="attributes[${attr.id}]"
                       class="input w-full"
                       ${requiredAttr}>
            `;
    }

    

    const div = document.createElement('div');

    div.className = 'flex flex-col';

    div.innerHTML = `
        <label class="text-sm font-medium text-gray-700 mb-1 flex items-center">
            ${attr.name}
            ${requiredStar}
            
            ${unitBadge}
        </label>

        ${fieldHtml}

        ${
            attr.is_required
            ? `<span class="text-[11px] text-red-400 mt-1">
                    This field is required for this category
               </span>`
            : ''
        }
    `;

    container.appendChild(div);

});
            }

        }
    }
</script>