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
            console.log('Load attributes for category:', categoryId);
        }
    }
}
</script>