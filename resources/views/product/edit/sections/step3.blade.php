<form method="POST"
    action="{{ route('supplier.products.update-step1', [
          'product' => $product->id,
          'step' => 3
      ]) }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">


    @include('product.edit.partials.custom-attribute-form', [
    'customAttributes' => $customAttributes,
    'product' => $product,])

    {{-- ================= MATERIALS ================= --}}
    <h3 class="text-xl font-semibold mb-4 mt-6">Materials Used</h3>

    {{-- Контейнер для выбранных материалов (чипсы) --}}
    <div id="selected-materials" class="flex flex-wrap gap-2 mb-2"></div>

    {{-- Поиск --}}
    <input type="text" id="materialSearch" placeholder="Search materials..." class="w-full mb-2 border rounded-lg border-gray-300 px-3 py-2 text-sm text-gray-600">

    {{-- Список всех материалов для выбора --}}
    <div id="materials-options" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
        @foreach($materialsPrepared as $material)
        <button type="button"
            class="material-option px-1 py-1 border rounded bg-white shadow-sm"
            data-id="{{ $material['id'] }}"
            data-name="{{ $material['translations'][app()->getLocale()]['name'] ?? '' }}">
            {{ $material['translations'][app()->getLocale()]['name'] ?? '' }}
        </button>
        @endforeach
    </div>


    <input type="hidden"
        name="materials_selected"
        id="materialsSelectedInput"
        value="{{ implode(',', $product->materials->pluck('id')->toArray()) }}">


    <div class="flex justify-between">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 2]) }}"
            class="mt-4 text-white bg-gray-600 hover:bg-gray-400 text-white px-6 py-2 rounded">
            Previous
        </a>



        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded">
            Next
        </button>

    </div>

</form>