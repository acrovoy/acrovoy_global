<div class="space-y-4">

    <div>
        <label class="text-sm text-gray-600">Key</label>
        <input
            type="text"
            name="key"
            value="{{ old('key', $requirement->key ?? '') }}"
            class="w-full border rounded-lg px-3 py-2 text-sm"
            placeholder="e.g. material, size, voltage"
        >
    </div>

    <div>
        <label class="text-sm text-gray-600">Value</label>
        <input
            type="text"
            name="value"
            value="{{ old('value', $requirement->value ?? '') }}"
            class="w-full border rounded-lg px-3 py-2 text-sm"
            placeholder="e.g. steel, M10, 220V"
        >
    </div>

    <div class="flex gap-4">

        <div class="w-1/2">
            <label class="text-sm text-gray-600">Type</label>
            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="boolean">Boolean</option>
            </select>
        </div>

        <div class="w-1/2 flex items-end">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="is_required" value="1">
                Required
            </label>
        </div>

    </div>

</div>