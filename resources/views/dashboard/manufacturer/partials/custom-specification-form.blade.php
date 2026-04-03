<div class="">
     <h3 class="text-xl font-semibold mb-4">Custom specifications</h3>

     <div id="specs-step-4" class="space-y-4">

         {{-- SPEC #0 --}}
         <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-0">
             <h4 class="font-semibold mb-3">Specification</h4>

             {{-- Parameter --}}
             <div class="mb-4">
                 <label class="block mb-1 font-medium">Parameter</label>

                 @foreach($languages as $index => $language)
                 @php
                     $flagPath = "/images/flags/svg/".strtolower($language->code).".svg";
                 @endphp
                 @if($index === 0)
                 <div class="flex items-center gap-2 mb-1">
                     <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                     <input type="text"
                         name="specs[0][{{ $language->code }}][key]"
                         placeholder="Parameter ({{ $language->code }})"
                         class="input mb-2 w-full">
                 </div>
                 @else
                 <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                     <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                     <input type="text"
                         name="specs[0][{{ $language->code }}][key]"
                         placeholder="Parameter ({{ $language->code }})"
                         class="input mb-2 w-full">
                 </div>
                 @endif
                 @endforeach
             </div>

             {{-- Value --}}
             <div>
                 <label class="block mb-1 font-medium">Value</label>

                 @foreach($languages as $index => $language)
                 @php
                     $flagPath = "/images/flags/svg/".strtolower($language->code).".svg";
                 @endphp
                 @if($index === 0)
                 <div class="flex items-center gap-2 mb-1">
                     <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                     <input type="text"
                         name="specs[0][{{ $language->code }}][value]"
                         placeholder="Value ({{ $language->code }})"
                         class="input mb-2 w-full">
                 </div>
                 @else
                 <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                     <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                     <input type="text"
                         name="specs[0][{{ $language->code }}][value]"
                         placeholder="Value ({{ $language->code }})"
                         class="input mb-2 w-full">
                 </div>
                 @endif
                 @endforeach
             </div>

             {{-- Languages toggle --}}
             @if(count($languages) > 1)
             <button type="button"
                 @click="open = !open"
                 class="mt-3 text-sm text-blue-600 hover:underline flex items-center gap-1">
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
             @endif
         </div>

     </div>

     <button type="button"
         onclick="addSpec('specs-step-4')"
         class="mt-3 text-blue-700 font-medium">
         + Add specification
     </button>

</div>

<script>
     /**
      * Определяем стартовый индекс спецификаций
      * Для edit-формы — берём количество уже существующих specs
      * Для create — начнётся с 1
      */
     specIndex = Date.now()

     /**
      * Добавление спецификации
      */
     function addSpec(containerId) {
         const container = document.getElementById(containerId);
         if (!container) return;

         let html = `
        <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-${specIndex}">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold">Specification</h4>
                <button type="button"
                        onclick="removeSpec(${specIndex})"
                        class="text-red-600 hover:text-red-800 font-semibold">
                    ✕
                </button>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Parameter</label>
        `;

         @foreach($languages as $index => $language)
         @php
             $flagPath = "/images/flags/svg/".strtolower($language->code).".svg";
         @endphp
         @if($index === 0)
         html += `
                <div class="flex items-center gap-2 mb-1">
                    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                    <input type="text"
                           name="specs[${specIndex}][{{ $language->code }}][key]"
                           class="input mb-2 w-full"
                           placeholder="Parameter ({{ $language->code }})">
                </div>
            `;
         @else
         html += `
                <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                    <input type="text"
                           name="specs[${specIndex}][{{ $language->code }}][key]"
                           class="input mb-2 w-full"
                           placeholder="Parameter ({{ $language->code }})">
                </div>
            `;
         @endif
         @endforeach

         html += `
            </div>

            <div>
                <label class="block mb-1 font-medium">Value</label>
        `;

         @foreach($languages as $index => $language)
         @php
             $flagPath = "/images/flags/svg/".strtolower($language->code).".svg";
         @endphp
         @if($index === 0)
         html += `
                <div class="flex items-center gap-2 mb-1">
                    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                    <input type="text"
                           name="specs[${specIndex}][{{ $language->code }}][value]"
                           class="input mb-2 w-full"
                           placeholder="Value ({{ $language->code }})">
                </div>
            `;
         @else
         html += `
                <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                    <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                    <input type="text"
                           name="specs[${specIndex}][{{ $language->code }}][value]"
                           class="input mb-2 w-full"
                           placeholder="Value ({{ $language->code }})">
                </div>
            `;
         @endif
         @endforeach

         html += `
            </div>

            <button type="button"
                    @click="open = !open"
                    class="mt-3 text-sm text-blue-600 hover:underline flex items-center gap-1">
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
        `;

         container.insertAdjacentHTML('beforeend', html);

         // 🔴 КРИТИЧНО: инициализация Alpine для динамически добавленного блока
         if (window.Alpine) {
             Alpine.initTree(container.lastElementChild);
         }

         specIndex++;
     }

     /**
      * Удаление спецификации
      */
     function removeSpec(index) {
         const el = document.getElementById(`spec-${index}`);
         if (el) {
             el.remove();
         }
     }
 </script>