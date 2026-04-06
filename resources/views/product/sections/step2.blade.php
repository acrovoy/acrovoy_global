<div x-data="categorySelector({ initialCategory: {{ $product->category_id ?? 'null' }} })" x-init="init()">

    <h3 class="text-xl font-semibold mb-4">Category</h3>

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

            <div id="category-attributes" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>

            <div id="category-attributes-empty" class="hidden text-sm text-gray-400 italic">
                No specifications available for this category
            </div>
        </div>
    </div>

</div>

<script>
function categorySelector({ initialCategory = null } = {}) {
    return {
        levels: [],
        selectedCategory: null,
        breadcrumb: [],
        initialCategory,

        async init() {
            const res = await fetch('/dashboard/category-selector/root');
            const data = await res.json();
            this.levels = [{ items: data, selected: null }];

            if (this.initialCategory) {
                await this.loadPath(this.initialCategory);
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

                this.levels[i].items = [node]; // текущий node
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
            // Здесь fetch для атрибутов и их заполнение
            const res = await fetch(`/dashboard/category-selector/attributes/${categoryId}`);
            const attributes = await res.json();

            const container = document.getElementById('category-attributes');
            const emptyBlock = document.getElementById('category-attributes-empty');

            container.innerHTML = '';
            if (!attributes.length) {
                emptyBlock.classList.remove('hidden');
                return;
            }
            emptyBlock.classList.add('hidden');

            attributes.forEach(attr => {
                const requiredStar = attr.is_required ? `<span class="text-red-500 ml-1">*</span>` : '';
                const requiredAttr = attr.is_required ? 'required' : '';
                const unitBadge = attr.unit ? `<span class="ml-2 text-[10px] text-red-400">(${attr.unit})</span>` : '';

                let fieldHtml = `<input type="text" name="attributes[${attr.id}]" class="input w-full" ${requiredAttr}>`;
                if (attr.type === 'number') {
                    fieldHtml = `<input type="number" name="attributes[${attr.id}]" class="input w-full" ${requiredAttr}>`;
                } else if (attr.type === 'select' && attr.options) {
                    const optionsHtml = attr.options.map(o => `<option value="${o.value}">${o.label}</option>`).join('');
                    fieldHtml = `<select name="attributes[${attr.id}]" class="input w-full" ${requiredAttr}><option value="">Select...</option>${optionsHtml}</select>`;
                }

                const div = document.createElement('div');
                div.className = 'flex flex-col';
                div.innerHTML = `<label class="text-sm font-medium text-gray-700 mb-1 flex items-center">
                                    ${attr.name}${requiredStar}${unitBadge}
                                </label>${fieldHtml}`;
                container.appendChild(div);
            });
        }
    }
}
</script>