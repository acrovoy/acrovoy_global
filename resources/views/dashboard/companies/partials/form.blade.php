<x-alerts />

<form method="POST"
      action="{{ $company
            ? route('dashboard.companies.update', $company->id)
            : route('dashboard.companies.store') }}"
      class="space-y-4 bg-gray-50 border rounded-xl shadow-sm p-6">

    @csrf

    @if($company)
        @method('PUT')
    @endif

    {{-- COMPANY TYPE --}}
<div>
    <label class="block mb-1 text-sm font-medium text-gray-700">
        Company Type
    </label>

    @if($company)
        {{-- EDIT MODE (LOCKED) --}}
        <input type="text"
               class="input bg-gray-100 text-gray-600 cursor-not-allowed"
               value="{{ ucfirst($company->type) }}"
               disabled>

        <input type="hidden" name="type" value="{{ $company->type }}">

    @else
        {{-- CREATE MODE (SELECTABLE) --}}
        <select name="type" class="input" required>
            <option value="buyer" {{ old('type', $type ?? '') == 'buyer' ? 'selected' : '' }}>
                Buyer
            </option>
            <option value="supplier" {{ old('type', $type ?? '') == 'supplier' ? 'selected' : '' }}>
                Supplier
            </option>
            <option value="logistics" {{ old('type', $type ?? '') == 'logistics' ? 'selected' : '' }}>
                Logistics
            </option>
        </select>
    @endif
</div>

    {{-- NAME --}}
    <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">
            Company Name
        </label>

        <input type="text"
               name="name"
               id="name"
               class="input"
               value="{{ old('name', $company->name ?? '') }}"
               placeholder="Enter company name">
    </div>

    {{-- SLUG --}}
    <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">
            Slug
        </label>

        <input type="text"
               name="slug"
               id="slug"
               class="input"
               value="{{ old('slug', $company->slug ?? '') }}"
               placeholder="auto-generated-or-editable">
    </div>

    {{-- SUBMIT --}}
    <button class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
        Save Company
    </button>

</form>

{{-- SIMPLE SLUG GENERATOR --}}
<script>
document.getElementById('name').addEventListener('input', function () {
    let slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

    document.getElementById('slug').value = slug;
});
</script>

<style>
.input {
    width: 100%;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    font-family: 'Figtree', sans-serif;
    transition: 0.2s;
}
.input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
    outline: none;
}
</style>