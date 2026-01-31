<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Code --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Currency code
        </label>
        <input type="text"
               name="code"
               value="{{ old('code', $currency->code ?? '') }}"
               maxlength="3"
               class="w-full border rounded px-3 py-2 font-mono uppercase"
               placeholder="USD">
        <p class="text-xs text-gray-500 mt-1">
            ISO 4217 code (e.g. USD, EUR, GBP)
        </p>
    </div>

    {{-- Symbol --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Symbol
        </label>
        <input type="text"
               name="symbol"
               value="{{ old('symbol', $currency->symbol ?? '') }}"
               maxlength="5"
               class="w-full border rounded px-3 py-2"
               placeholder="$">
    </div>

    {{-- Name --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Currency name
        </label>
        <input type="text"
               name="name"
               value="{{ old('name', $currency->name ?? '') }}"
               class="w-full border rounded px-3 py-2"
               placeholder="US Dollar">
    </div>

</div>

<hr class="my-6">

{{-- Flags --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Active --}}
    <label class="flex items-start gap-3">
        <input type="checkbox"
               name="is_active"
               value="1"
               {{ old('is_active', $currency->is_active ?? false) ? 'checked' : '' }}
               class="mt-1">
        <div>
            <div class="text-sm font-medium">Active</div>
            <div class="text-xs text-gray-500">
                Available for users
            </div>
        </div>
    </label>

    {{-- Default --}}
    <label class="flex items-start gap-3">
        <input type="checkbox"
               name="is_default"
               value="1"
               {{ old('is_default', $currency->is_default ?? false) ? 'checked' : '' }}
               class="mt-1">
        <div>
            <div class="text-sm font-medium">Default currency</div>
            <div class="text-xs text-gray-500">
                Used when user has no selected currency
            </div>
        </div>
    </label>

    {{-- Priority --}}
    <label class="flex items-start gap-3">
        <input type="checkbox"
               name="is_priority"
               value="1"
               {{ old('is_priority', $currency->is_priority ?? false) ? 'checked' : '' }}
               class="mt-1">
        <div>
            <div class="text-sm font-medium">Priority currency</div>
            <div class="text-xs text-gray-500">
                Displayed at the top of currency selector
            </div>
        </div>
    </label>

</div>

<hr class="my-6">

{{-- Business metadata --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Priority level --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Business priority
        </label>
        <select name="priority"
                class="w-full border rounded px-3 py-2">
            @foreach(['low', 'medium', 'high'] as $level)
                <option value="{{ $level }}"
                    {{ old('priority', $currency->priority ?? 'medium') === $level ? 'selected' : '' }}>
                    {{ ucfirst($level) }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">
            Internal business segmentation
        </p>
    </div>

    {{-- Notes --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Notes
        </label>
        <input type="text"
               name="notes"
               value="{{ old('notes', $currency->notes ?? '') }}"
               class="w-full border rounded px-3 py-2"
               placeholder="Optional internal notes">
    </div>

</div>
