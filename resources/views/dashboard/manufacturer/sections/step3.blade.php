@include('dashboard.manufacturer.partials.custom-specification-form')


{{-- Materials Used --}}
<h3 class="text-xl font-semibold mb-4 mt-6">Materials Used</h3>
<div id="selected-materials" class="flex flex-wrap gap-2 mb-2"></div>
<input type="text" id="materialSearch" placeholder="Search materials..." class="w-full mb-2 border rounded-lg border-gray-300 px-3 py-2 text-sm text-gray-600">
<div id="materials-options" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
    @foreach($materials as $material)
        <button type="button" class="shadow-sm material-option px-1 py-1 border rounded bg-white" data-id="{{ $material->id }}" data-name="{{ $material->translations->first()?->name ?? $material->name }}">
            {{ $material->translations->first()?->name ?? $material->name }}
        </button>
    @endforeach
</div>
<input type="hidden" name="materials_selected" id="materialsSelectedInput">