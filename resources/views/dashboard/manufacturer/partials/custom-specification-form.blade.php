<div class="">
     <h3 class="text-xl font-semibold mb-4">Custom specifications</h3>

     <div id="specs-step-4" class="space-y-4">

         {{-- SPEC #0 --}}
         <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-0">
             <h4 class="font-semibold mb-3">Specification
                <x-help-tooltip width="w-80">
    <div class="space-y-2 leading-relaxed">
        <div class="font-semibold text-white">Пользовательские характеристики</div>
        <div class="text-gray-200 text-sm">
            В этом разделе вы можете добавить свои уникальные параметры для товара, 
            если стандартные характеристики категории не подходят или их недостаточно.
        </div>
        <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
            <li>Каждое поле можно заполнить на нескольких языках.</li>
            <li>Вы можете добавлять любое количество параметров.</li>
            <li>Если подходящей категории нет, создайте пользовательскую и заполните поля вручную.</li>
        </ul>
        <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
            Пример: <span class="text-gray-200">
                Для «Уличный стол» добавьте: <br>
                • <strong>Parameter:</strong> Материал столешницы — <strong>Value:</strong> Эпоксидная смола<br>
                
            </span>
        </div>
    </div>
</x-help-tooltip>
        
             </h4>

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
                     <div x-data="charCounter(60)" class="relative w-full">

    <input
        type="text"
        maxlength="60"
        class="input w-full"
        placeholder="Parameter ({{ $language->code }})"
        @input="update($event.target)"
        x-init="update($el)"
        style="padding-right: 4rem;"
    >

    <div
        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
        :class="color"
    >
        <span x-text="count"></span>/<span x-text="max"></span>
    </div>

</div>
                 </div>
                 @else
                 <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                     <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                     <div x-data="charCounter(60)" class="relative w-full">

    <input
        type="text"
        name="specs[0][{{ $language->code }}][key]"
        placeholder="Parameter ({{ $language->code }})"
        class="input mb-2 w-full"
        maxlength="60"
        @input="update($event.target)"
        x-init="update($el)"
        style="padding-right: 4rem;"
    >

    <div
        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
        :class="color"
    >
        <span x-text="count"></span>/<span x-text="max"></span>
    </div>

</div>
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
                     <div x-data="charCounter(60)" class="relative w-full">

    <input
        type="text"
        name="specs[0][{{ $language->code }}][value]"
        placeholder="Value ({{ $language->code }})"
        class="input mb-2 w-full"
        maxlength="60"
        @input="update($event.target)"
        x-init="update($el)"
        style="padding-right: 4rem;"
    >

    <div
        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
        :class="color"
    >
        <span x-text="count"></span>/<span x-text="max"></span>
    </div>

</div>
                 </div>
                 @else
                 <div x-show="open" x-collapse class="flex items-center gap-2 mb-2">
                     <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                     <div x-data="charCounter(60)" class="relative w-full">

    <input
        type="text"
        name="specs[0][{{ $language->code }}][value]"
        placeholder="Value ({{ $language->code }})"
        class="input mb-2 w-full"
        maxlength="60"
        @input="update($event.target)"
        x-init="update($el)"
        style="padding-right: 4rem;"
    >

    <div
        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
        :class="color"
    >
        <span x-text="count"></span>/<span x-text="max"></span>
    </div>

</div>
                 </div>
                 @endif
                 @endforeach
             </div>

             {{-- Languages toggle --}}
             @if(count($languages) > 1)
             <div class="flex justify-between mt-1">

        <div class="text-xs text-gray-500 italic">
            * Optional, maximum 60 characters
        </div>

             <button type="button"
                 @click="open = !open"
                 class="mt-3 text-xs text-blue-600 hover:underline flex items-center gap-1">
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
specIndex = Date.now()

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

<img src="{{ $flagPath }}" class="w-5 h-5 rounded">

<div x-data="charCounter(60)" class="relative w-full">

<input
type="text"
name="specs[${specIndex}][{{ $language->code }}][key]"
class="input mb-2 w-full"
placeholder="Parameter ({{ $language->code }})"
maxlength="60"
@input="update($event.target)"
x-init="update($el)"
style="padding-right:4rem;"
>

<div
class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
:class="color"
>

<span x-text="count"></span>/<span x-text="max"></span>

</div>

</div>

</div>
`;

@else

html += `
<div x-show="open" x-collapse class="flex items-center gap-2 mb-2">

<img src="{{ $flagPath }}" class="w-5 h-5 rounded">

<div x-data="charCounter(60)" class="relative w-full">

<input
type="text"
name="specs[${specIndex}][{{ $language->code }}][key]"
class="input mb-2 w-full"
placeholder="Parameter ({{ $language->code }})"
maxlength="60"
@input="update($event.target)"
x-init="update($el)"
style="padding-right:4rem;"
>

<div
class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
:class="color"
>

<span x-text="count"></span>/<span x-text="max"></span>

</div>

</div>

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

<img src="{{ $flagPath }}" class="w-5 h-5 rounded">

<div x-data="charCounter(60)" class="relative w-full">

<input
type="text"
name="specs[${specIndex}][{{ $language->code }}][value]"
class="input mb-2 w-full"
placeholder="Value ({{ $language->code }})"
maxlength="60"
@input="update($event.target)"
x-init="update($el)"
style="padding-right:4rem;"
>

<div
class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
:class="color"
>

<span x-text="count"></span>/<span x-text="max"></span>

</div>

</div>

</div>
`;

@else

html += `
<div x-show="open" x-collapse class="flex items-center gap-2 mb-2">

<img src="{{ $flagPath }}" class="w-5 h-5 rounded">

<div x-data="charCounter(60)" class="relative w-full">

<input
type="text"
name="specs[${specIndex}][{{ $language->code }}][value]"
class="input mb-2 w-full"
placeholder="Value ({{ $language->code }})"
maxlength="60"
@input="update($event.target)"
x-init="update($el)"
style="padding-right:4rem;"
>

<div
class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
:class="color"
>

<span x-text="count"></span>/<span x-text="max"></span>

</div>

</div>

</div>
`;

@endif
@endforeach

html += `
</div>

<button
type="button"
@click="open = !open"
class="mt-3 text-sm text-blue-600 hover:underline flex items-center gap-1"
>

Other Languages

<svg
:class="{ 'rotate-180': open }"
class="w-4 h-4 transition-transform"
fill="none"
stroke="currentColor"
viewBox="0 0 24 24"
>

<path
stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M19 9l-7 7-7-7"
/>

</svg>

</button>

</div>
`;

container.insertAdjacentHTML('beforeend', html);

if (window.Alpine) {
    requestAnimationFrame(() => {
        Alpine.initTree(container.lastElementChild);
    });
}

specIndex++;

}

function removeSpec(index) {

const el = document.getElementById(`spec-${index}`);

if (el) el.remove();

}
</script>