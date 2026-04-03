let specIndex = Date.now();

function addSpec(containerId) {
    const container = document.getElementById(containerId);
    if (!container || !window.appLanguages) return;

    const index = specIndex++;

    let html = `
    <div x-data="{ open:false }"
         class="border rounded p-4 bg-white"
         id="spec-${index}">

        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold">Specification</h4>

            <button type="button"
                onclick="removeSpec(${index})"
                class="text-red-600 font-semibold">
                ✕
            </button>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-medium">Parameter</label>
    `;

    window.appLanguages.forEach((lang, idx) => {
        let flagPath = `/images/flags/svg/${lang.toLowerCase()}.svg`; // путь к флагам

        if (idx === 0) {
            html += `
            <div class="flex items-center gap-2 mb-1">
                <img src="${flagPath}" alt="${lang}" class="w-5 h-5 rounded">
                <input type="text"
                    name="specs[${index}][${lang}][key]"
                    class="input mb-2 w-full"
                    placeholder="Parameter (${lang})">
            </div>
            `;
        } else {
            html += `
            <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                <img src="${flagPath}" alt="${lang}" class="w-5 h-5 rounded">
                <input type="text"
                    name="specs[${index}][${lang}][key]"
                    class="input mb-2 w-full"
                    placeholder="Parameter (${lang})">
            </div>
            `;
        }
    });

    html += `</div><div>
        <label class="block mb-1 font-medium">Value</label>
    `;

    window.appLanguages.forEach((lang, idx) => {
        let flagPath = `/images/flags/svg/${lang.toLowerCase()}.svg`; // путь к флагам

        if (idx === 0) {
            html += `
            <div class="flex items-center gap-2 mb-1">
                <img src="${flagPath}" alt="${lang}" class="w-5 h-5 rounded">
                <input type="text"
                    name="specs[${index}][${lang}][value]"
                    class="input mb-2 w-full"
                    placeholder="Value (${lang})">
            </div>
            `;
        } else {
            html += `
            <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                <img src="${flagPath}" alt="${lang}" class="w-5 h-5 rounded">
                <input type="text"
                    name="specs[${index}][${lang}][value]"
                    class="input mb-2 w-full"
                    placeholder="Value (${lang})">
            </div>
            `;
        }
    });

    html += `
    <button type="button"
        @click="open = !open"
        class="mt-3 text-sm text-blue-600 hover:underline flex items-center gap-1">

        <span>Other Languages</span>

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
    `;

    container.insertAdjacentHTML('beforeend', html);

    const el = container.lastElementChild;

    if (window.Alpine) {
        Alpine.initTree(el);
    }
}

function removeSpec(index) {
    document.getElementById(`spec-${index}`)?.remove();
}

window.addSpec = addSpec;
window.removeSpec = removeSpec;