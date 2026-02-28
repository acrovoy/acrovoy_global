

<form action="{{ route('manufacturer.company.update') }}"
      method="POST"
      enctype="multipart/form-data"
      class="max-w-7xl mx-auto">

@csrf

{{-- ================= MAIN PROFILE CARD ================= --}}

<div class="bg-white border rounded-2xl shadow-sm p-8 space-y-10 mt-4">
    <div class="text-sm text-gray-400 uppercase tracking-wider">
    Identity & Description
</div>



{{-- ================= BASIC COMPANY INFO ================= --}}

<div class="grid lg:grid-cols-3 gap-10">

    {{-- Logo --}}
    <div class="space-y-4">

        <label class="block font-medium text-sm">
            Company Logo
        </label>

        <div id="logo-dropzone"
             class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-xl
                    flex items-center justify-center bg-gray-50 relative cursor-pointer
                    overflow-hidden group">

            <img id="logo-preview"
                 src="{{ $company->logo()?->cdn_url ?? asset('images/no-logo.png') }}"
                 class="w-full h-full object-cover">

            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center
                        opacity-0 group-hover:opacity-100 transition rounded-xl">
                <span class="text-white text-xs">Change</span>
            </div>
        </div>

        <input type="file" name="logo" accept="image/*" id="logo-input" class="hidden">

        <p class="text-xs text-gray-400">
            Drag & drop or click to upload
        </p>
    </div>



    {{-- Core Fields --}}
    <div class="lg:col-span-2 space-y-6">

        <div>
            <label class="block font-medium mb-2">Company Name</label>

            <input type="text"
                   name="name"
                   value="{{ old('name', $company->name ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl p-3"
                   required>
        </div>


        <div>
            <label class="block font-medium mb-2">
                Registration Country
            </label>

            <select name="country_id"
                    class="w-full border border-gray-300 rounded-xl p-3">

                <option value="">Select country</option>

                @foreach($countries as $country)
                <option value="{{ $country->id }}"
                    @selected(old('country_id', $company->country_id ?? null) == $country->id)>
                    {{ $country->name }}
                </option>
                @endforeach

            </select>
        </div>

        <div>
            <label class="block font-medium mb-2">
                Short Listing Description
            </label>

            <input type="text"
                   name="short_description"
                   value="{{ old('short_description', $company->short_description ?? '') }}"
                   maxlength="255"
                   class="w-full border border-gray-300 rounded-xl p-3">

            <p class="text-xs text-gray-400 mt-1">
                Short text for catalog cards (max 35 chars recommended)
            </p>
        </div>



        <div>
            <label class="block font-medium mb-2">
                Company Description
            </label>

            <textarea name="description"
                      rows="5"
                      class="w-full border border-gray-300 rounded-xl p-3">{{ old('description', $company->description ?? '') }}</textarea>
        </div>



        <h3 class="font-medium">
Supplier Classification
</h3>



<div id="selected-supplier-types" class="flex flex-wrap gap-2 mb-2"></div>

<input type="text"
       id="supplierTypeSearch"
       placeholder="Search supplier types..."
       class="w-full border rounded-xl px-4 py-2 text-sm mb-3">

<div id="supplier-types-options"
     class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">

    @foreach($supplierTypes as $type)

        @php
        $name = $type->translation?->name ?? $type->slug;
        @endphp

        <button type="button"
                class="supplier-type-option border rounded-xl bg-white text-xs p-3
                    hover:bg-gray-50 transition shadow-sm"
                data-id="{{ $type->id }}"
                data-name="{{ $name }}">

            {{ $name }}
        </button>

    @endforeach

</div>

<input type="hidden"
       name="supplier_types_selected"
       id="supplierTypesSelectedInput">





    </div>

</div>



{{-- ================= General Information ================= --}}

<div class="pt-8 border-t space-y-8">

    <div class="text-sm text-gray-400 uppercase tracking-wider">
        General Information
    </div>

    {{-- About us --}}
    <h3 class="font-medium mb-2">
        About us
    </h3>

    <div>
        <textarea name="about_us_description"
                  rows="5"
                  class="w-full border border-gray-300 rounded-xl p-3">{{ old('about_us_description', $company->about_us_description ?? '') }}</textarea>
    </div>


    {{-- Company Metrics --}}
    <div class="grid sm:grid-cols-2 gap-6">

        {{-- Founded --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Founded
            </label>
            <input type="number"
                   name="founded_year"
                   placeholder="e.g. 2015"
                   value="{{ old('founded_year', $company->founded_year ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

        {{-- Annual Export Revenue --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Annual Export Revenue (USD)
            </label>
            <input type="number"
                   name="annual_export_revenue"
                   placeholder="e.g. 650000"
                   value="{{ old('annual_export_revenue', $company->annual_export_revenue ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

        {{-- Total Employees --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Total Employees
            </label>
            <input type="number"
                   name="total_employees"
                   placeholder="e.g. 50"
                   value="{{ old('total_employees', $company->total_employees ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

        {{-- Company Registration Capital --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Company Registration Capital (USD or local currency)
            </label>
            <input type="number"
                   name="registration_capital"
                   placeholder="e.g. 1000000"
                   value="{{ old('registration_capital', $company->registration_capital ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

    </div>



    {{-- Export Markets --}}
    <h3 class="font-medium mb-2">
        Export Markets
    </h3>

    <div id="selected-export-markets" class="flex flex-wrap gap-2 mb-2"></div>

    <input type="text"
           id="exportMarketSearch"
           placeholder="Search export markets..."
           class="w-full border rounded-xl px-4 py-2 text-sm mb-3">

    <div id="export-markets-options"
         class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">

        @foreach($exportMarkets as $market)

            @php
                $name = $market->translation?->name ?? $market->slug;
            @endphp

            <button type="button"
                    class="export-market-option border rounded-xl bg-white text-xs p-3
                           hover:bg-gray-50 transition shadow-sm"
                    data-id="{{ $market->id }}"
                    data-name="{{ $name }}">

                {{ $name }}

            </button>

        @endforeach

    </div>

    <input type="hidden"
           name="export_markets_selected"
           id="exportMarketsSelectedInput">

</div>



{{-- ================= MANUFACTURING PROFILE ================= --}}
<div class="pt-10 border-t space-y-8">

    <div class="text-sm text-gray-400 uppercase tracking-wider">
        Manufacturing Profile
    </div>

    {{-- Manufacturing Overview --}}
    <div>
        <label class="block text-sm font-medium mb-2">
            Manufacturing Overview
        </label>

        <textarea name="manufacturing_description"
                  rows="6"
                  class="w-full border border-gray-300 rounded-xl p-3 text-sm">{{ old('manufacturing_description', $company->manufacturing_description ?? '') }}</textarea>
    </div>


    {{-- Production Metrics --}}
    <div class="grid sm:grid-cols-2 gap-6">

        

        {{-- Factory Area --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Factory Area (m²)
            </label>

            <input type="number"
                   name="factory_area"
                   placeholder="e.g. 2500"
                   value="{{ old('factory_area', $company->factory_area ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

        {{-- Production Lines --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Production Lines
            </label>

            <input type="number"
                   name="production_lines"
                   placeholder="e.g. 4"
                   value="{{ old('production_lines', $company->production_lines ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

        {{-- MOQ --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                MOQ (Minimum Order Quantity)
            </label>

            <input type="number"
                   name="moq"
                   placeholder="e.g. 50"
                   value="{{ old('moq', $company->moq ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

        {{-- Monthly Production Capacity --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Monthly Production Capacity (units)
            </label>

            <input type="number"
                   name="monthly_capacity"
                   placeholder="e.g. 1200"
                   value="{{ old('monthly_capacity', $company->monthly_capacity ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

        {{-- Average Lead Time --}}
        <div>
            <label class="block text-sm font-medium mb-2">
                Average Lead Time (days)
            </label>

            <input type="number"
                   name="lead_time_days"
                   placeholder="e.g. 30"
                   value="{{ old('lead_time_days', $company->lead_time_days ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm">
        </div>

    </div>

</div>



{{-- ================= CONTACT BLOCK ================= --}}

<div class="pt-8 border-t space-y-6">
<div class="text-sm text-gray-400 uppercase tracking-wider">
    Contact Information
</div>


<div class="grid md:grid-cols-2 gap-6">

    



    <div>
        <label class="block font-medium mb-2">Company Email</label>

        <input type="email"
               name="email"
               required
               value="{{ old('email', $company->email ?? '') }}"
               class="w-full border border-gray-300 rounded-xl p-3">
    </div>



    <div>
        <label class="block font-medium mb-2">Company Phone</label>

        <input type="text"
               name="phone"
               value="{{ old('phone', $company->phone ?? '') }}"
               class="w-full border border-gray-300 rounded-xl p-3">
    </div>



    <div>
        <label class="block font-medium mb-2">Company Address</label>

        <textarea name="address"
                  class="w-full border border-gray-300 rounded-xl p-3">{{ old('address', $company->address ?? '') }}</textarea>
    </div>

</div>

</div>



{{-- ================= CATALOG VISUAL BLOCK ================= --}}

<div class="grid lg:grid-cols-2 gap-12 pt-10 border-t">
<div class="col-span-full text-sm text-gray-400 uppercase tracking-wider">
    Catalog Presentation
</div>
<div class="space-y-4">

    <div class="flex flex-col items-center space-y-2">
        
        <div id="catalog-dropzone"
             class="w-64 h-64 border-2 border-dashed border-gray-300
                    rounded-xl flex items-center justify-center
                    bg-gray-50 relative cursor-pointer
                    overflow-hidden group">

            <img id="catalog-preview"
                 src="{{ $company->catalog_image ? asset('storage/' . $company->catalog_image) : asset('images/no-image.png') }}"
                 class="w-full h-full object-cover">

            <div class="absolute inset-0 bg-black/30 flex items-center justify-center
                        opacity-0 group-hover:opacity-100 transition">
                <span class="text-white text-sm">Change</span>
            </div>

            <input type="file" name="catalog_image" accept="image/*" id="catalog-input" class="hidden">
        </div>

        <p class="text-xs text-gray-400 text-center">
            Used in supplier catalog cards
        </p>

    </div>

</div>

    {{-- Preview Card --}}
<div class="flex justify-center items-start">
    @include('dashboard.manufacturer.partials.preview-card')
</div>
    

</div>



{{-- ================= SUBMIT ================= --}}

<div class="pt-10 border-t">

<button type="submit"
        class="w-full bg-blue-950 hover:bg-blue-900 text-white py-4
               rounded-xl text-lg font-semibold transition">

Save Company Profile

</button>

</div>



</div>

</form>

{{-- JS для drag&drop превью --}}
<script>
const logoInput = document.getElementById('logo-input');
const logoPreview = document.getElementById('logo-preview');
const dropzone = document.getElementById('logo-dropzone');

// Клик по зоне открывает выбор файла
dropzone.addEventListener('click', () => logoInput.click());

// Обработка выбора файла
logoInput.addEventListener('change', function(event) {
    const [file] = event.target.files;
    if (file) {
        logoPreview.src = URL.createObjectURL(file);
    }
});

// Drag & drop
dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.classList.add('border-blue-400', 'bg-blue-50');
});

dropzone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropzone.classList.remove('border-blue-400', 'bg-blue-50');
});

dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.classList.remove('border-blue-400', 'bg-blue-50');

    const file = e.dataTransfer.files[0];
    if (file) {
        logoInput.files = e.dataTransfer.files; // присвоить input
        logoPreview.src = URL.createObjectURL(file);
    }
});
</script>

<script>
const catalogInput = document.getElementById('catalog-input');
const catalogPreview = document.getElementById('catalog-preview');
const catalogDropzone = document.getElementById('catalog-dropzone');

catalogDropzone.addEventListener('click', () => catalogInput.click());

catalogInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (file) catalogPreview.src = URL.createObjectURL(file);
});

catalogDropzone.addEventListener('dragover', e => {
    e.preventDefault();
    catalogDropzone.classList.add('border-blue-400', 'bg-blue-50');
});

catalogDropzone.addEventListener('dragleave', () => {
    catalogDropzone.classList.remove('border-blue-400', 'bg-blue-50');
});

catalogDropzone.addEventListener('drop', e => {
    e.preventDefault();
    catalogDropzone.classList.remove('border-blue-400', 'bg-blue-50');

    const file = e.dataTransfer.files[0];
    if (file) {
        catalogInput.files = e.dataTransfer.files;
        catalogPreview.src = URL.createObjectURL(file);
    }
});
</script>

<script>

/* ===========================
 * SUPPLIER TYPES CHIP SELECTOR
 * =========================== */

const selectedTypesContainer = document.getElementById('selected-supplier-types');
const typeOptions = document.querySelectorAll('.supplier-type-option');
const selectedTypesInput = document.getElementById('supplierTypesSelectedInput');
const typeSearch = document.getElementById('supplierTypeSearch');
const supplierTypeMap = @json(
    $supplierTypes->mapWithKeys(function($type){

        return [
            (string)$type->id =>
                $type->translation?->name ?? $type->slug
        ];

    })
);

let selectedTypes = @json(
    collect($selectedTypes)->map(fn($id)=>[
        'id'=>(string)$id,
        'name'=>''
    ])
);

/* Render chips */
function renderSelectedTypes() {
    selectedTypesContainer.innerHTML = '';

    selectedTypes.forEach((item, index) => {

        const chip = document.createElement('div');
        chip.className = 'px-2 py-1 bg-blue-100 text-blue-800 text-xs flex items-center gap-1 shadow-sm';

        chip.innerHTML = `
            <span>${item.name}</span>
            <button type="button" class="hover:text-red-600">&times;</button>
        `;

        chip.querySelector('button').onclick = () => {
            selectedTypes.splice(index, 1);
            updateSelectedTypesInput();
            renderSelectedTypes();
        };

        selectedTypesContainer.appendChild(chip);
    });
}

/* Sync hidden input */
function updateSelectedTypesInput() {
    selectedTypesInput.value = selectedTypes.map(t => t.id).join(',');
}

/* Type option click */
typeOptions.forEach(btn => {
    btn.addEventListener('click', () => {

        const id = btn.dataset.id;
        const name = btn.dataset.name;

        if (!selectedTypes.find(t => t.id === id)) {
            selectedTypes.push({ id, name });
        }

        updateSelectedTypesInput();
        renderSelectedTypes();
    });
});

/* Search filter */
if (typeSearch) {
    typeSearch.addEventListener('input', function () {

        const search = this.value.toLowerCase();

        typeOptions.forEach(btn => {

            const name = btn.dataset.name.toLowerCase();

            btn.style.display = name.includes(search) ? '' : 'none';
        });
    });
}

function initSelectedTypes() {

    selectedTypes = selectedTypes.map(item => ({
        id: item.id,
        name: supplierTypeMap[item.id] || item.name
    }));

    updateSelectedTypesInput();
    renderSelectedTypes();
}

initSelectedTypes();
</script>

<script>
window.initialExportMarkets = @json(
    $company->exportMarkets->map(function($market){
        return [
            'id' => (string) $market->id,
            'name' => $market->translation?->name ?? $market->slug
        ];
    })
);
</script>

<script>

/* ===========================
 * EXPORT MARKETS CHIP SELECTOR
 * =========================== */

const selectedExportContainer = document.getElementById('selected-export-markets');
const exportOptions = document.querySelectorAll('.export-market-option');
const exportSelectedInput = document.getElementById('exportMarketsSelectedInput');
const exportSearch = document.getElementById('exportMarketSearch');

/* === Initialize from backend data === */
let selectedExportMarkets = typeof window.initialExportMarkets !== 'undefined'
    ? window.initialExportMarkets
    : [];

/* Render chips */
function renderExportMarkets() {

    selectedExportContainer.innerHTML = '';

    selectedExportMarkets.forEach((item, index) => {

        const chip = document.createElement('div');
        chip.className = 'px-2 py-1 bg-blue-100 text-blue-800 text-xs flex items-center gap-1 shadow-sm';

        chip.innerHTML = `
            <span>${item.name}</span>
            <button type="button" class="hover:text-red-600">&times;</button>
        `;

        chip.querySelector('button').onclick = () => {
            selectedExportMarkets.splice(index, 1);
            updateExportInput();
            renderExportMarkets();
        };

        selectedExportContainer.appendChild(chip);
    });
}

/* Sync hidden input */
function updateExportInput() {
    exportSelectedInput.value =
        selectedExportMarkets.map(m => m.id).join(',');
}

/* Click selection */
exportOptions.forEach(btn => {

    btn.addEventListener('click', () => {

        const id = btn.dataset.id;
        const name = btn.dataset.name;

        if (!selectedExportMarkets.find(m => m.id === id)) {
            selectedExportMarkets.push({ id, name });
        }

        updateExportInput();
        renderExportMarkets();
    });

});

/* Search filter */
if (exportSearch) {

    exportSearch.addEventListener('input', function () {

        const search = this.value.toLowerCase();

        exportOptions.forEach(btn => {

            const name = btn.dataset.name.toLowerCase();

            btn.style.display =
                name.includes(search) ? '' : 'none';
        });
    });
}

/* Initial render */
document.addEventListener('DOMContentLoaded', () => {
    renderExportMarkets();
    updateExportInput();
});

</script>
