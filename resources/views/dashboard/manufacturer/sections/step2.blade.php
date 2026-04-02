<div x-data="categorySelector()" x-init="init()">

    <h3 class="text-xl font-semibold mb-4">Category</h3>

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

    <div x-show="breadcrumb.length" class="text-sm text-gray-600 mt-3">
        Selected category: <span class="font-medium" x-text="breadcrumb.join(' → ')"></span>
    </div>


    <div id="category-attributes" class="mt-4"></div>


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

            this.levels = [{ items: data }]; // <-- исправлено
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
                this.levels.push({ items: children });
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
            container.innerHTML = '';

            attributes.forEach(attr => {
                let fieldHtml = '';

                switch(attr.type) {
                    case 'text':
                        fieldHtml = `<input type="text" name="attributes[${attr.id}]" class="input w-full" />`;
                        break;

                    case 'number':
                        fieldHtml = `<input type="number" name="attributes[${attr.id}]" class="input w-full" />`;
                        break;

                    case 'select':
                        if(attr.options) {
                            const optionsHtml = attr.options
                                .map(o => `<option value="${o.value}">${o.label}</option>`)
                                .join('');
                            fieldHtml = `<select name="attributes[${attr.id}]" class="input w-full">${optionsHtml}</select>`;
                        }
                        break;

                    case 'multiselect':
                        if(attr.options) {
                            const optionsHtml = attr.options
                                .map(o => `
                                    <label class="inline-flex items-center mr-4">
                                        <input type="checkbox" name="attributes[${attr.id}][]" value="${o.value}" class="mr-1" />
                                        ${o.label}
                                    </label>
                                `)
                                .join('');
                            fieldHtml = `<div class="flex flex-wrap">${optionsHtml}</div>`;
                        }
                        break;

                    case 'boolean':
                        fieldHtml = `<input type="checkbox" name="attributes[${attr.id}]" value="1" />`;
                        break;

                    default:
                        fieldHtml = `<input type="text" name="attributes[${attr.id}]" class="input w-full" />`;
                }

                const div = document.createElement('div');
                div.className = 'mb-3';
                div.innerHTML = `<label class="block text-sm font-medium text-gray-700 mb-1">${attr.name}</label>${fieldHtml}`;

                container.appendChild(div);
            });
        }
    }
}
</script>