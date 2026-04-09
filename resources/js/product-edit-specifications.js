let specIndex = Date.now();

function addSpec(containerId, existingData = {}) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const index = specIndex++;

    let html = `
    <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-${index}">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold">Specification</h4>
            <button type="button" onclick="removeSpec(${index})" class="text-red-600 hover:text-red-800 font-semibold">✕</button>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-medium">Parameter</label>
    `;

    window.appLanguages.forEach((lang, idx) => {
        const value = existingData[lang]?.key || '';
        const flagPath = `/images/flags/svg/${lang.toLowerCase()}.svg`;
        html += `
        <div ${idx === 0 ? '' : 'x-show=\"open\" x-collapse'} class="flex items-center gap-2 mb-2">
            <img src="${flagPath}" class="w-5 h-5 rounded">
            <div x-data="{count: ${value.length}, max: 60}" class="relative w-full">
                <input type="text"
                       name="specs[${index}][${lang}][key]"
                       class="input mb-2 w-full"
                       placeholder="Parameter (${lang})"
                       maxlength="60"
                       @input="count = $event.target.value.length"
                       x-init="$el.value='${value}'; count = $el.value.length"
                       style="padding-right:4rem;">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                     :class="count >= max ? 'text-yellow-400' : 'text-gray-400'">
                    <span x-text="count"></span>/<span x-text="max"></span>
                </div>
            </div>
        </div>
        `;
    });

    html += `</div><div><label class="block mb-1 font-medium">Value</label>`;

    window.appLanguages.forEach((lang, idx) => {
        const value = existingData[lang]?.value || '';
        const flagPath = `/images/flags/svg/${lang.toLowerCase()}.svg`;
        html += `
        <div ${idx === 0 ? '' : 'x-show=\"open\" x-collapse'} class="flex items-center gap-2 mb-2">
            <img src="${flagPath}" class="w-5 h-5 rounded">
            <div x-data="{count: ${value.length}, max: 60}" class="relative w-full">
                <input type="text"
                       name="specs[${index}][${lang}][value]"
                       class="input mb-2 w-full"
                       placeholder="Value (${lang})"
                       maxlength="60"
                       @input="count = $event.target.value.length"
                       x-init="$el.value='${value}'; count = $el.value.length"
                       style="padding-right:4rem;">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                     :class="count >= max ? 'text-yellow-400' : 'text-gray-400'">
                    <span x-text="count"></span>/<span x-text="max"></span>
                </div>
            </div>
        </div>
        `;
    });

    html += `
        </div>
        ${window.appLanguages.length > 1 ? `
        <button type="button" @click="open = !open" class="mt-3 text-sm text-blue-600 hover:underline flex items-center gap-1">
            Other Languages
            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>` : ''}
    </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    if (window.Alpine) {
        requestAnimationFrame(() => Alpine.initTree(container.lastElementChild));
    }
}

function removeSpec(index) {
    const el = document.getElementById(`spec-${index}`);
    if (el) el.remove();
}

window.addSpec = addSpec;
window.removeSpec = removeSpec;